<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Modalidade: string implements HasLabel
{
    case Futsal = 'futsal';
    case Society = 'society';

    public function getLabel(): string
    {
        return match ($this) {
            self::Futsal => 'Futsal (quadra)',
            self::Society => 'Society (campo)',
        };
    }

    /** Quantidade padrão de jogadores de linha por time (sem contar goleiro). */
    public function jogadoresPorTimePadrao(): int
    {
        return match ($this) {
            self::Futsal => 4,   // 4 de linha + goleiro = 5
            self::Society => 6,  // 6 de linha + goleiro = 7
        };
    }

    public function usaGoleiroPadrao(): bool
    {
        return true;
    }
}
