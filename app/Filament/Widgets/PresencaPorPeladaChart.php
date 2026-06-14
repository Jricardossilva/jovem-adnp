<?php

namespace App\Filament\Widgets;

use App\Models\Pelada;
use Filament\Widgets\ChartWidget;

class PresencaPorPeladaChart extends ChartWidget
{
    protected static ?string $heading = 'Presença nas últimas peladas';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $peladas = Pelada::query()
            ->withCount(['inscricoes as presentes_count' => fn ($q) => $q->where('presente', true)])
            ->orderByDesc('data')
            ->limit(8)
            ->get()
            ->reverse()
            ->values();

        return [
            'datasets' => [[
                'label' => 'Presentes',
                'data' => $peladas->pluck('presentes_count')->all(),
                'backgroundColor' => '#10b981',
                'borderColor' => '#059669',
            ]],
            'labels' => $peladas->map(fn ($p) => $p->data->format('d/m'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}