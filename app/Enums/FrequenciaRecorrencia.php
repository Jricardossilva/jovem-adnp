<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum FrequenciaRecorrencia: string implements HasLabel
{
    case Semanal = 'semanal';
    case Quinzenal = 'quinzenal';
    case Mensal = 'mensal';

    public function getLabel(): string
    {
        return match ($this) {
            self::Semanal => 'Semanal',
            self::Quinzenal => 'Quinzenal',
            self::Mensal => 'Mensal',
        };
    }

    public function diasIntervalo(): int
    {
        return match ($this) {
            self::Semanal => 7,
            self::Quinzenal => 14,
            self::Mensal => 30,
        };
    }
}
