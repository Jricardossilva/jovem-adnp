<?php

namespace App\Filament\Resources\AtletaResource\Pages;

use App\Filament\Concerns\RedirecionaParaListagem;
use App\Filament\Resources\AtletaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAtleta extends CreateRecord
{
    use RedirecionaParaListagem;
    protected static string $resource = AtletaResource::class;
}
