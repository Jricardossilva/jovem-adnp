<?php

namespace App\Filament\Resources\PeladaResource\Pages;

use App\Filament\Concerns\RedirecionaParaListagem;
use App\Enums\StatusPelada;
use App\Filament\Resources\PeladaResource;
use App\Models\Pelada;
use App\Services\FrequenciaService;
use App\Services\SorteioService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPelada extends EditRecord
{
    use RedirecionaParaListagem;    
    protected static string $resource = PeladaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('abrir_lista')
                ->label('Abrir lista')->icon('heroicon-o-lock-open')->color('success')
                ->visible(fn (Pelada $r) => $r->status !== StatusPelada::Aberta && $r->status !== StatusPelada::Encerrada)
                ->action(fn (Pelada $r) => $r->update(['status' => StatusPelada::Aberta->value])),

            Action::make('fechar_lista')
                ->label('Fechar lista')->icon('heroicon-o-lock-closed')->color('warning')
                ->visible(fn (Pelada $r) => $r->status === StatusPelada::Aberta)
                ->action(fn (Pelada $r) => $r->update(['status' => StatusPelada::Fechada->value])),

            Action::make('sortear')
                ->label('Sortear times')->icon('heroicon-o-sparkles')->color('primary')
                ->visible(fn (Pelada $r) => $r->status !== StatusPelada::Encerrada)
                ->requiresConfirmation()
                ->modalDescription('Sorteia os times com os atletas marcados como presentes. Refaz caso já tenha sorteado.')
                ->action(function (Pelada $r) {
                    try {
                        $resultado = app(SorteioService::class)->sortear($r);
                        $corpo = $resultado['times']->map(function ($time) {
                            $nomes = $time->atletas->map(fn ($a) => $a->pivot->e_goleiro ? "🧤 {$a->nome}" : $a->nome)->implode(', ');

                            return "{$time->nome}: {$nomes}";
                        })->implode("\n");

                        if ($resultado['reservas']->isNotEmpty()) {
                            $corpo .= "\nReservas: ".$resultado['reservas']->pluck('nome')->implode(', ');
                        }

                        Notification::make()->title('Times sorteados!')->body($corpo)->success()->persistent()->send();
                    } catch (\Throwable $e) {
                        Notification::make()->title('Não foi possível sortear')->body($e->getMessage())->danger()->send();
                    }
                }),

            Action::make('encerrar')
                ->label('Encerrar pelada')->icon('heroicon-o-flag')->color('danger')
                ->visible(fn (Pelada $r) => $r->status !== StatusPelada::Encerrada)
                ->requiresConfirmation()
                ->modalDescription('Processa a frequência: zera faltas de quem veio, soma falta de quem não veio e inativa quem atingir o limite. Não pode ser desfeito.')
                ->action(function (Pelada $r) {
                    $resultado = app(FrequenciaService::class)->processarEncerramento($r);
                    $msg = 'Frequência processada.';
                    if (! empty($resultado['inativados'])) {
                        $msg .= ' Inativados: '.implode(', ', $resultado['inativados']);
                    }
                    Notification::make()->title('Pelada encerrada')->body($msg)->success()->send();
                }),

            DeleteAction::make(),
        ];
    }
}
