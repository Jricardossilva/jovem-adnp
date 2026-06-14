<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatusAtleta: string implements HasLabel, HasColor
{
    case Ativo = 'ativo';
    case Suspenso = 'suspenso';
    case Inativo = 'inativo';

    public function getLabel(): string
    {
        return match ($this) {
            self::Ativo => 'Ativo',
            self::Suspenso => 'Suspenso',
            self::Inativo => 'Inativo',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Ativo => 'success',
            self::Suspenso => 'danger',
            self::Inativo => 'gray',
        };
    }
}
