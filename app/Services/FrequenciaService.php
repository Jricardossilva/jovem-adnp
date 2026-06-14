<?php

namespace App\Services;

use App\Enums\SituacaoCadastro;
use App\Enums\StatusAtleta;
use App\Enums\StatusPelada;
use App\Models\Atleta;
use App\Models\Pelada;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FrequenciaService
{
    /** Número de faltas consecutivas que inativa o atleta automaticamente. */
    public const LIMITE_FALTAS = 5;

    /**
     * Processa o encerramento de uma pelada:
     *  - quem esteve presente: zera as faltas consecutivas;
     *  - quem NÃO participou (ativo e aprovado): +1 falta. "Ausência" conta tanto
     *    para quem se inscreveu e faltou quanto para quem nem entrou na lista.
     *  - ao atingir o limite, o atleta é inativado automaticamente.
     *
     * Atletas suspensos ou já inativos não acumulam faltas.
     */
    public function processarEncerramento(Pelada $pelada): array
    {
        if ($pelada->status === StatusPelada::Encerrada) {
            return ['ja_encerrada' => true];
        }

        return DB::transaction(function () use ($pelada) {
            $presentesIds = $pelada->inscricoes()
                ->where('presente', true)
                ->pluck('atleta_id')
                ->all();

            $inativados = collect();

            $atletas = Atleta::query()
                ->where('status', StatusAtleta::Ativo->value)
                ->where('situacao_cadastro', SituacaoCadastro::Aprovado->value)
                ->get();

            foreach ($atletas as $atleta) {
                // Suspenso na data da pelada não acumula falta.
                if ($atleta->estaSuspenso(Carbon::parse($pelada->data))) {
                    continue;
                }

                if (in_array($atleta->id, $presentesIds, true)) {
                    if ($atleta->faltas_consecutivas !== 0) {
                        $atleta->update(['faltas_consecutivas' => 0]);
                    }

                    continue;
                }

                $faltas = $atleta->faltas_consecutivas + 1;

                if ($faltas >= self::LIMITE_FALTAS) {
                    $atleta->update([
                        'faltas_consecutivas' => $faltas,
                        'status' => StatusAtleta::Inativo->value,
                    ]);
                    $inativados->push($atleta);
                } else {
                    $atleta->update(['faltas_consecutivas' => $faltas]);
                }
            }

            $pelada->update([
                'status' => StatusPelada::Encerrada->value,
                'encerrada_em' => now(),
            ]);

            return [
                'presentes' => count($presentesIds),
                'inativados' => $inativados->pluck('nome')->all(),
            ];
        });
    }

    /**
     * Relatório de frequência por atleta dentro de um período (peladas encerradas).
     *
     * @return Collection<int, array>
     */
    public function relatorio(?Carbon $inicio = null, ?Carbon $fim = null): Collection
    {
        $peladasQuery = Pelada::query()->where('status', StatusPelada::Encerrada->value);

        if ($inicio) {
            $peladasQuery->whereDate('data', '>=', $inicio);
        }
        if ($fim) {
            $peladasQuery->whereDate('data', '<=', $fim);
        }

        $peladas = $peladasQuery->pluck('id');
        $totalPeladas = $peladas->count();

        return Atleta::query()
            ->withCount(['inscricoes as presencas_count' => function ($q) use ($peladas) {
                $q->where('presente', true)->whereIn('pelada_id', $peladas);
            }])
            ->orderBy('nome')
            ->get()
            ->map(function (Atleta $atleta) use ($totalPeladas) {
                $presencas = (int) ($atleta->presencas_count ?? 0);
                $percentual = $totalPeladas > 0 ? round(($presencas / $totalPeladas) * 100, 1) : 0.0;

                return [
                    'atleta_id' => $atleta->id,
                    'nome' => $atleta->nomeExibicao(),
                    'status' => $atleta->status,
                    'presencas' => $presencas,
                    'total_peladas' => $totalPeladas,
                    'ausencias' => max(0, $totalPeladas - $presencas),
                    'percentual' => $percentual,
                    'faltas_consecutivas' => $atleta->faltas_consecutivas,
                ];
            });
    }
}
