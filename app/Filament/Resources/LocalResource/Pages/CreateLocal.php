<?php

namespace App\Filament\Resources\LocalResource\Pages;

use App\Filament\Concerns\RedirecionaParaListagem;
use App\Filament\Resources\LocalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLocal extends CreateRecord
{
    use RedirecionaParaListagem;
    protected static string $resource = LocalResource::class;
}
