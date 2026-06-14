<?php

namespace App\Filament\Resources\VersiculoResource\Pages;

use App\Filament\Concerns\RedirecionaParaListagem;
use App\Filament\Resources\VersiculoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVersiculo extends EditRecord
{
    use RedirecionaParaListagem;
    protected static string $resource = VersiculoResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
