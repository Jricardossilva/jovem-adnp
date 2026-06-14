<?php

namespace App\Filament\Resources\RecorrenciaResource\Pages;

use App\Filament\Concerns\RedirecionaParaListagem;
use App\Filament\Resources\RecorrenciaResource;
use Filament\Resources\Pages\EditRecord;

class EditRecorrencia extends EditRecord
{
    use RedirecionaParaListagem;
    protected static string $resource = RecorrenciaResource::class;
    protected function getHeaderActions(): array { return [\Filament\Actions\DeleteAction::make()]; }
}
