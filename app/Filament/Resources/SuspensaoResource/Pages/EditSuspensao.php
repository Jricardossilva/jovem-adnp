<?php

namespace App\Filament\Resources\SuspensaoResource\Pages;

use App\Filament\Concerns\RedirecionaParaListagem;
use App\Filament\Resources\SuspensaoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSuspensao extends EditRecord
{
    use RedirecionaParaListagem;
    protected static string $resource = SuspensaoResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
