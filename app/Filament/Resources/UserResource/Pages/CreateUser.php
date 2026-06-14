<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Concerns\RedirecionaParaListagem;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    use RedirecionaParaListagem;

    protected static string $resource = UserResource::class;
}