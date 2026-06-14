<?php

namespace App\Filament\Resources\PeladaResource\RelationManagers;

use App\Models\Atleta;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class InscricoesRelationManager extends RelationManager
{
    protected static string $relationship = 'inscricoes';

    protected static ?string $title = 'Lista / Presença';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('atleta_id')->label('Atleta')
                ->options(fn () => Atleta::query()->aprovados()->ativos()->orderBy('nome')->pluck('nome', 'id'))
                ->searchable()->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('atleta.nome')->label('Atleta')->searchable()
                    ->description(fn ($record) => $record->atleta?->e_goleiro ? 'Goleiro' : null),
                IconColumn::make('presente')->label('Presente')->boolean(),
                TextColumn::make('origem')->label('Origem')->badge()->toggleable(),
            ])
            ->headerActions([
                CreateAction::make()->label('Adicionar atleta')
                    ->mutateFormDataUsing(function (array $data) {
                        $data['origem'] = 'organizador';

                        return $data;
                    }),
            ])
            ->actions([
                Action::make('presenca')
                    ->label(fn ($record) => $record->presente ? 'Presente' : 'Marcar presença')
                    ->icon(fn ($record) => $record->presente ? 'heroicon-s-check-circle' : 'heroicon-o-check-circle')
                    ->color(fn ($record) => $record->presente ? 'success' : 'gray')
                    ->action(fn ($record) => $record->update([
                        'presente' => ! $record->presente,
                        'confirmado_em' => $record->presente ? null : now(),
                    ])),
                DeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                BulkAction::make('marcar_presentes')->label('Marcar como presentes')
                    ->icon('heroicon-o-check-circle')->color('success')
                    ->action(fn (Collection $records) => $records->each->update(['presente' => true, 'confirmado_em' => now()])),
                BulkAction::make('desmarcar')->label('Desmarcar presença')
                    ->icon('heroicon-o-x-circle')->color('gray')
                    ->action(fn (Collection $records) => $records->each->update(['presente' => false, 'confirmado_em' => null])),
            ])
            ->defaultSort('atleta_id');
    }
}
