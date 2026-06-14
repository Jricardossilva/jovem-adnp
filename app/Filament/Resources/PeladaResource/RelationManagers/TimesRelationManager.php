<?php

namespace App\Filament\Resources\PeladaResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TimesRelationManager extends RelationManager
{
    protected static string $relationship = 'times';

    protected static ?string $title = 'Times sorteados';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nome')
            ->columns([
                TextColumn::make('nome')->label('Time')->badge()
                    ->color(fn ($record) => match ($record->cor) {
                        'Azul' => 'info', 'Vermelho' => 'danger', 'Verde' => 'success',
                        'Amarelo' => 'warning', default => 'gray',
                    }),
                TextColumn::make('atletas')->label('Jogadores')->wrap()
                    ->getStateUsing(fn ($record) => $record->atletas
                        ->map(fn ($a) => $a->pivot->e_goleiro ? "🧤 {$a->nome}" : $a->nome)
                        ->implode(', ')),
            ])
            ->defaultSort('ordem');
    }
}
