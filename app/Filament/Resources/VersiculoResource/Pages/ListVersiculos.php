<?php

namespace App\Filament\Resources\VersiculoResource\Pages;

use App\Filament\Resources\VersiculoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVersiculos extends ListRecords
{
    protected static string $resource = VersiculoResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
