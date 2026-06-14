<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatusPelada: string implements HasLabel, HasColor
{
    case Agendada = 'agendada';   // criada, lista ainda não aberta
    case Aberta = 'aberta';       // atletas podem entrar na lista
    case Fechada = 'fechada';     // lista fechada, em dia de jogo / sorteio
    case Encerrada = 'encerrada'; // finalizada, frequência processada

    public function getLabel(): string
    {
        return match ($this) {
            self::Agendada => 'Agendada',
            self::Aberta => 'Lista aberta',
            self::Fechada => 'Lista fechada',
            self::Encerrada => 'Encerrada',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Agendada => 'gray',
            self::Aberta => 'success',
            self::Fechada => 'warning',
            self::Encerrada => 'info',
        };
    }
}
