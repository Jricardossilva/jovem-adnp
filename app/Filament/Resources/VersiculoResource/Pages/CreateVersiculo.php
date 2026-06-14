<?php

namespace App\Filament\Resources\VersiculoResource\Pages;

use App\Filament\Concerns\RedirecionaParaListagem;
use App\Filament\Resources\VersiculoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVersiculo extends CreateRecord
{
    use RedirecionaParaListagem;
    protected static string $resource = VersiculoResource::class;
}
