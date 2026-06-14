<?php

namespace App\Filament\Resources\AtletaResource\Pages;

use App\Enums\SituacaoCadastro;
use App\Filament\Resources\AtletaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListAtletas extends ListRecords
{
    protected static string $resource = AtletaResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }

    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todos'),
            'pendentes' => Tab::make('Pendentes')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('situacao_cadastro', SituacaoCadastro::Pendente->value))
                ->badge(\App\Models\Atleta::pendentes()->count()),
            'ativos' => Tab::make('Ativos')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'ativo')),
            'inativos' => Tab::make('Inativos')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'inativo')),
            'suspensos' => Tab::make('Suspensos')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'suspenso')),
        ];
    }
}
