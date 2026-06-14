<?php

namespace App\Filament\Resources\SuspensaoResource\Pages;

use App\Filament\Resources\SuspensaoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSuspensoes extends ListRecords
{
    protected static string $resource = SuspensaoResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->mutateFormDataUsing(function (array $data) {
            $data['criado_por'] = auth()->id();

            return $data;
        })];
    }
}
