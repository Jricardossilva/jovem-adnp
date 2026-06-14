<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum MetodoSorteio: string implements HasLabel
{
    case Aleatorio = 'aleatorio';
    case Balanceado = 'balanceado';

    public function getLabel(): string
    {
        return match ($this) {
            self::Aleatorio => 'Aleatório',
            self::Balanceado => 'Balanceado por nível',
        };
    }
}
