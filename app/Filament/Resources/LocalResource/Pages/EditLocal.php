<?php

namespace App\Filament\Resources\LocalResource\Pages;

use App\Filament\Concerns\RedirecionaParaListagem;
use App\Filament\Resources\LocalResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLocal extends EditRecord
{
    use RedirecionaParaListagem;
    protected static string $resource = LocalResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
