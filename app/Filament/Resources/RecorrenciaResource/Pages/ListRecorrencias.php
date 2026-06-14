<?php

namespace App\Filament\Resources\RecorrenciaResource\Pages;

use App\Filament\Resources\RecorrenciaResource;
use Filament\Resources\Pages\ListRecords;

class ListRecorrencias extends ListRecords
{
    protected static string $resource = RecorrenciaResource::class;
    protected function getHeaderActions(): array { return [\Filament\Actions\CreateAction::make()]; }
}
