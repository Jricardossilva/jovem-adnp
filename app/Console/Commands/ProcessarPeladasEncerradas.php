<?php

namespace App\Console\Commands;

use App\Enums\StatusPelada;
use App\Models\Pelada;
use App\Services\FrequenciaService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ProcessarPeladasEncerradas extends Command
{
    protected $signature = 'peladas:processar-encerradas';

    protected $description = 'Encerra automaticamente peladas cuja data já passou e processa a frequência/faltas.';

    public function handle(FrequenciaService $frequencia): int
    {
        $peladas = Pelada::query()
            ->whereIn('status', [StatusPelada::Aberta->value, StatusPelada::Fechada->value])
            ->whereDate('data', '<', Carbon::today())
            ->get();

        foreach ($peladas as $pelada) {
            $resultado = $frequencia->processarEncerramento($pelada);
            $this->info("Pelada #{$pelada->id} ({$pelada->data->format('d/m/Y')}) encerrada. "
                .'Inativados: '.implode(', ', $resultado['inativados'] ?? []));
        }

        $this->info('Total processado: '.$peladas->count());

        return self::SUCCESS;
    }
}
