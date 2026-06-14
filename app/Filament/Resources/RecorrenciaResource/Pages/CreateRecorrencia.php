<?php

namespace App\Filament\Resources\RecorrenciaResource\Pages;

use App\Filament\Concerns\RedirecionaParaListagem;
use App\Filament\Resources\RecorrenciaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecorrencia extends CreateRecord
{
    use RedirecionaParaListagem;
    protected static string $resource = RecorrenciaResource::class;

}
