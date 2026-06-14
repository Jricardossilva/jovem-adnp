<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Concerns\RedirecionaParaListagem;
use App\Filament\Resources\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    use RedirecionaParaListagem;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}