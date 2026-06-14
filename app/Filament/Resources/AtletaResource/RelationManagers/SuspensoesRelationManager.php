<?php

namespace App\Filament\Resources\AtletaResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SuspensoesRelationManager extends RelationManager
{
    protected static string $relationship = 'suspensoes';

    protected static ?string $title = 'Suspensões';

    public function form(Form $form): Form
    {
        return $form->schema([
            DatePicker::make('inicio')->label('Início')->required(),
            DatePicker::make('fim')->label('Fim')->required()->after('inicio'),
            Textarea::make('motivo')->label('Motivo')->required()->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('motivo')
            ->columns([
                TextColumn::make('inicio')->label('Início')->date('d/m/Y'),
                TextColumn::make('fim')->label('Fim')->date('d/m/Y'),
                TextColumn::make('motivo')->label('Motivo')->limit(50)->wrap(),
                TextColumn::make('criadoPor.name')->label('Por')->toggleable(),
            ])
            ->headerActions([
                CreateAction::make()->mutateFormDataUsing(function (array $data) {
                    $data['criado_por'] = auth()->id();

                    return $data;
                }),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->defaultSort('inicio', 'desc');
    }
}
