<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SituacaoCadastro: string implements HasLabel, HasColor
{
    case Pendente = 'pendente';
    case Aprovado = 'aprovado';
    case Rejeitado = 'rejeitado';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pendente => 'Pendente de aprovação',
            self::Aprovado => 'Aprovado',
            self::Rejeitado => 'Rejeitado',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pendente => 'warning',
            self::Aprovado => 'success',
            self::Rejeitado => 'danger',
        };
    }
}
