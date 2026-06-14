<?php

namespace App\Filament\Resources\PeladaResource\Pages;

use App\Filament\Concerns\RedirecionaParaListagem;
use App\Filament\Resources\PeladaResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePelada extends CreateRecord
{
    use RedirecionaParaListagem; 
    protected static string $resource = PeladaResource::class;
}
