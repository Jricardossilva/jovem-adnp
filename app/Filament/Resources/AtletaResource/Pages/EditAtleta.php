<?php

namespace App\Filament\Resources\AtletaResource\Pages;

use App\Filament\Concerns\RedirecionaParaListagem;
use App\Filament\Resources\AtletaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAtleta extends EditRecord
{
    use RedirecionaParaListagem;
    protected static string $resource = AtletaResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
