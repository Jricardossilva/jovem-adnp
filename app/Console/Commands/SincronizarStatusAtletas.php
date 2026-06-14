<?php

namespace App\Console\Commands;

use App\Enums\SituacaoCadastro;
use App\Enums\StatusAtleta;
use App\Models\Atleta;
use Illuminate\Console\Command;

class SincronizarStatusAtletas extends Command
{
    protected $signature = 'atletas:sincronizar-status';

    protected $description = 'Marca atletas como suspensos durante a vigência da suspensão e os devolve para ativo ao terminar.';

    public function handle(): int
    {
        $suspensos = 0;
        $reativados = 0;

        Atleta::query()
            ->where('situacao_cadastro', SituacaoCadastro::Aprovado->value)
            ->where('status', '!=', StatusAtleta::Inativo->value) // inativo só volta manualmente
            ->chunkById(200, function ($atletas) use (&$suspensos, &$reativados) {
                foreach ($atletas as $atleta) {
                    $vigente = $atleta->estaSuspenso();

                    if ($vigente && $atleta->status !== StatusAtleta::Suspenso) {
                        $atleta->update(['status' => StatusAtleta::Suspenso->value]);
                        $suspensos++;
                    } elseif (! $vigente && $atleta->status === StatusAtleta::Suspenso) {
                        $atleta->update(['status' => StatusAtleta::Ativo->value]);
                        $reativados++;
                    }
                }
            });

        $this->info("Suspensos: {$suspensos} | Reativados: {$reativados}");

        return self::SUCCESS;
    }
}
