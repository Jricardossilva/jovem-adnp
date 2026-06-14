<?php

namespace App\Console\Commands;

use App\Enums\StatusPelada;
use App\Models\Pelada;
use App\Models\Recorrencia;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GerarPeladasRecorrentes extends Command
{
    protected $signature = 'peladas:gerar-recorrentes {--dias=14 : Janela futura, em dias, para gerar peladas}';

    protected $description = 'Cria as próximas peladas a partir das recorrências ativas (já com lista aberta).';

    public function handle(): int
    {
        $janela = (int) $this->option('dias');
        $hoje = Carbon::today();
        $limite = $hoje->copy()->addDays($janela);
        $criadas = 0;

        Recorrencia::query()->where('ativo', true)->with('local')->each(function (Recorrencia $rec) use ($hoje, $limite, &$criadas) {
            for ($data = $hoje->copy(); $data->lte($limite); $data->addDay()) {
                if (! $this->dataPertenceARecorrencia($rec, $data)) {
                    continue;
                }

                $jaExiste = Pelada::query()
                    ->where('recorrencia_id', $rec->id)
                    ->whereDate('data', $data)
                    ->exists();

                if ($jaExiste) {
                    continue;
                }

                Pelada::create([
                    'recorrencia_id' => $rec->id,
                    'local_id' => $rec->local_id,
                    'modalidade' => $rec->modalidade->value,
                    'jogadores_por_time' => $rec->jogadores_por_time,
                    'com_goleiro' => $rec->com_goleiro,
                    'metodo_sorteio' => $rec->metodo_sorteio->value,
                    'data' => $data->toDateString(),
                    'horario' => $rec->horario,
                    'status' => StatusPelada::Aberta->value,
                    'exige_verificacao_telefone' => $rec->exige_verificacao_telefone,
                ]);

                $criadas++;
            }
        });

        $this->info("Peladas criadas: {$criadas}");

        return self::SUCCESS;
    }

    private function dataPertenceARecorrencia(Recorrencia $rec, Carbon $data): bool
    {
        // dia da semana (0=domingo ... 6=sábado) deve bater quando informado
        if ($rec->dia_semana !== null && (int) $data->dayOfWeek !== (int) $rec->dia_semana) {
            return false;
        }

        // Para semanal basta o dia da semana. Quinzenal/mensal usam o intervalo
        // a partir da última pelada gerada para a recorrência.
        if ($rec->frequencia->diasIntervalo() <= 7) {
            return true;
        }

        $ultima = Pelada::query()
            ->where('recorrencia_id', $rec->id)
            ->orderByDesc('data')
            ->first();

        if (! $ultima) {
            return true;
        }

        $dias = $ultima->data->diffInDays($data);

        return $dias >= $rec->frequencia->diasIntervalo();
    }
}
