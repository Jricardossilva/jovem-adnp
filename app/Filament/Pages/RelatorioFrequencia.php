<?php

namespace App\Filament\Pages;

use App\Services\FrequenciaService;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class RelatorioFrequencia extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Relatório de frequência';

    protected static ?string $navigationGroup = 'Peladas';

    protected static ?string $title = 'Relatório de frequência';

    protected static string $view = 'filament.pages.relatorio-frequencia';

    public ?string $inicio = null;

    public ?string $fim = null;

    public function mount(): void
    {
        $this->inicio = now()->startOfMonth()->subMonths(2)->toDateString();
        $this->fim = now()->toDateString();
    }

    public function getLinhasProperty()
    {
        $inicio = $this->inicio ? Carbon::parse($this->inicio) : null;
        $fim = $this->fim ? Carbon::parse($this->fim) : null;

        return app(FrequenciaService::class)->relatorio($inicio, $fim);
    }
}
