<?php

namespace App\Filament\Widgets;

use App\Enums\SituacaoCadastro;
use App\Enums\StatusAtleta;
use App\Enums\StatusPelada;
use App\Models\Atleta;
use App\Models\Pelada;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EstatisticasPeladas extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $atletasAtivos = Atleta::where('status', StatusAtleta::Ativo->value)
            ->where('situacao_cadastro', SituacaoCadastro::Aprovado->value)
            ->count();

        $pendentes = Atleta::where('situacao_cadastro', SituacaoCadastro::Pendente->value)->count();

        $peladasMes = Pelada::whereMonth('data', now()->month)
            ->whereYear('data', now()->year)
            ->count();

        $proxima = Pelada::with('local')
            ->whereDate('data', '>=', today())
            ->where('status', '!=', StatusPelada::Encerrada->value)
            ->orderBy('data')
            ->first();

        return [
            Stat::make('Atletas ativos', $atletasAtivos)
                ->description('Aprovados e ativos')
                ->color('success'),

            Stat::make('Cadastros pendentes', $pendentes)
                ->description('Aguardando aprovação')
                ->color($pendentes > 0 ? 'warning' : 'gray'),

            Stat::make('Peladas no mês', $peladasMes)
                ->description('Mês atual')
                ->color('info'),

            Stat::make('Próxima pelada', $proxima ? $proxima->data->format('d/m/Y') : '—')
                ->description($proxima?->local?->nome ?? 'Nenhuma agendada')
                ->color('primary'),
        ];
    }
}