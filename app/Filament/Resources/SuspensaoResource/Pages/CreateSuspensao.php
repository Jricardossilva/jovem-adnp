<?php

namespace App\Filament\Resources\SuspensaoResource\Pages;

use App\Filament\Concerns\RedirecionaParaListagem;
use App\Filament\Resources\SuspensaoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSuspensao extends CreateRecord
{
    use RedirecionaParaListagem;
    protected static string $resource = SuspensaoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['criado_por'] = auth()->id();

        return $data;
    }
}
