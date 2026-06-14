<?php

namespace App\Services;

use App\Enums\MetodoSorteio;
use App\Models\Atleta;
use App\Models\Pelada;
use App\Models\Time;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SorteioService
{
    /**
     * Sorteia os times de uma pelada a partir dos atletas presentes.
     * Refaz o sorteio caso já exista (apaga os times anteriores).
     *
     * @return array{times: Collection, reservas: Collection}
     */
    public function sortear(Pelada $pelada): array
    {
        $presentes = $pelada->inscricoes()
            ->where('presente', true)
            ->with('atleta')
            ->get()
            ->pluck('atleta')
            ->filter()                       // segurança contra atleta removido
            ->reject(fn (Atleta $a) => $a->estaSuspenso()) // nunca sorteia suspenso
            ->values();

        $total = $presentes->count();

        if ($total < 2) {
            throw new \RuntimeException('São necessários ao menos 2 atletas presentes para sortear.');
        }

        $capacidade = $pelada->totalPorTime();
        $numTimes = max(2, intdiv($total, max(1, $capacidade)));
        $vagas = $numTimes * $capacidade;

        // Separa goleiros e jogadores de linha
        $usaGoleiro = $pelada->com_goleiro;
        $goleiros = $usaGoleiro
            ? $presentes->filter(fn (Atleta $a) => $a->e_goleiro)->values()
            : collect();
        $linha = $usaGoleiro
            ? $presentes->reject(fn (Atleta $a) => $a->e_goleiro)->values()
            : $presentes->values();

        $balanceado = $pelada->metodo_sorteio === MetodoSorteio::Balanceado;

        $goleiros = $this->ordenar($goleiros, $balanceado);
        $linha = $this->ordenar($linha, $balanceado);

        // Buckets dos times
        $buckets = collect(range(0, $numTimes - 1))->mapWithKeys(fn ($i) => [$i => collect()]);
        $goleiroDoTime = []; // index do time => atleta_id do goleiro

        // 1) Um goleiro por time (na ordem). Goleiros sobrando viram jogadores de linha.
        foreach ($goleiros as $i => $goleiro) {
            if ($i < $numTimes) {
                $buckets[$i]->push($goleiro);
                $goleiroDoTime[$goleiro->id] = $i;
            } else {
                $linha->push($goleiro);
            }
        }

        // 2) Jogadores de linha distribuídos em "snake" (equilibra nível quando balanceado)
        $linha = $this->ordenar($linha, $balanceado);
        $this->distribuirSnake($linha, $buckets, $numTimes, $capacidade);

        // 3) Persiste
        $cores = ['Azul', 'Vermelho', 'Verde', 'Amarelo', 'Preto', 'Branco', 'Laranja', 'Roxo'];

        return DB::transaction(function () use ($pelada, $buckets, $goleiroDoTime, $cores, $usaGoleiro) {
            $pelada->times()->each(function (Time $t) {
                $t->atletas()->detach();
                $t->delete();
            });

            $timesCriados = collect();
            $atletasSorteados = collect();

            foreach ($buckets as $i => $atletas) {
                if ($atletas->isEmpty()) {
                    continue;
                }

                $time = $pelada->times()->create([
                    'nome' => 'Time '.($i + 1),
                    'cor' => $cores[$i] ?? null,
                    'ordem' => $i + 1,
                ]);

                foreach ($atletas as $atleta) {
                    $eGoleiro = $usaGoleiro && (($goleiroDoTime[$atleta->id] ?? null) === $i);
                    $time->atletas()->attach($atleta->id, ['e_goleiro' => $eGoleiro]);
                    $atletasSorteados->push($atleta->id);
                }

                $timesCriados->push($time->load('atletas'));
            }

            $pelada->update(['sorteio_realizado_em' => now()]);

            $reservas = $pelada->atletasPresentes()
                ->reject(fn (Atleta $a) => $atletasSorteados->contains($a->id))
                ->values();

            return ['times' => $timesCriados, 'reservas' => $reservas];
        });
    }

    private function ordenar(Collection $atletas, bool $balanceado): Collection
    {
        return $balanceado
            ? $atletas->sortByDesc('nivel')->values()
            : $atletas->shuffle()->values();
    }

    /**
     * Distribui atletas nos buckets em ordem de ida-e-volta (snake),
     * respeitando a capacidade-alvo de cada time.
     */
    private function distribuirSnake(Collection $atletas, Collection $buckets, int $numTimes, int $capacidade): void
    {
        $indice = 0;
        $crescente = true;

        foreach ($atletas as $atleta) {
            // pula times que já atingiram a capacidade
            $tentativas = 0;
            while ($buckets[$indice]->count() >= $capacidade && $tentativas < $numTimes) {
                [$indice, $crescente] = $this->proximoIndice($indice, $crescente, $numTimes);
                $tentativas++;
            }

            // se todos cheios, mantém distribuição (vira reserva natural ao exceder vagas)
            if ($tentativas >= $numTimes) {
                break;
            }

            $buckets[$indice]->push($atleta);
            [$indice, $crescente] = $this->proximoIndice($indice, $crescente, $numTimes);
        }
    }

    private function proximoIndice(int $indice, bool $crescente, int $numTimes): array
    {
        if ($crescente) {
            if ($indice + 1 < $numTimes) {
                return [$indice + 1, true];
            }

            return [$indice, false]; // vira e fica no mesmo (snake)
        }

        if ($indice - 1 >= 0) {
            return [$indice - 1, false];
        }

        return [$indice, true];
    }
}
