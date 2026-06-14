<?php

namespace App\Filament\Resources\PeladaResource\Pages;

use App\Filament\Resources\PeladaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPeladas extends ListRecords
{
    protected static string $resource = PeladaResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Nova pelada')];
    }
}
