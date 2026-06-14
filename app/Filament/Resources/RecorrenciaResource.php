<?php

namespace App\Filament\Resources;

use App\Enums\FrequenciaRecorrencia;
use App\Enums\Modalidade;
use App\Enums\MetodoSorteio;
use App\Filament\Resources\RecorrenciaResource\Pages;
use App\Models\Recorrencia;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RecorrenciaResource extends Resource
{
    protected static ?string $model = Recorrencia::class;

    protected static ?string $slug = 'recorrencias';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $modelLabel = 'recorrência';

    protected static ?string $pluralModelLabel = 'recorrências';

    protected static ?string $navigationLabel = 'Recorrências';

    protected static ?string $navigationGroup = 'Peladas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nome')->label('Nome')->required()->placeholder('Ex.: Pelada de quinta')->maxLength(120),
            Select::make('local_id')->label('Local')->relationship('local', 'nome')->required()->searchable()->preload(),
            Select::make('modalidade')->label('Modalidade')
                ->options(Modalidade::class)->default(Modalidade::Futsal)->required()->live()
                ->afterStateUpdated(function ($state, Set $set) {
                    if ($state) {
                        $modalidade = $state instanceof Modalidade ? $state : Modalidade::from($state);
                        $set('jogadores_por_time', $modalidade->jogadoresPorTimePadrao());
                        $set('com_goleiro', $modalidade->usaGoleiroPadrao());
                    }
                }),
            TextInput::make('jogadores_por_time')->label('Jogadores de linha por time')
                ->numeric()->minValue(2)->maxValue(11)->default(4)->required(),
            Toggle::make('com_goleiro')->label('Tem goleiro?')->default(true),
            Select::make('metodo_sorteio')->label('Método de sorteio')
                ->options(MetodoSorteio::class)->default(MetodoSorteio::Aleatorio)->required(),
            Select::make('frequencia')->label('Frequência')
                ->options(FrequenciaRecorrencia::class)->default(FrequenciaRecorrencia::Semanal)->required(),
            Select::make('dia_semana')->label('Dia da semana')
                ->options([
                    0 => 'Domingo', 1 => 'Segunda', 2 => 'Terça', 3 => 'Quarta',
                    4 => 'Quinta', 5 => 'Sexta', 6 => 'Sábado',
                ])->required(),
            TimePicker::make('horario')->label('Horário')->seconds(false)->required(),
            Toggle::make('exige_verificacao_telefone')->label('Exigir verificação por telefone')->default(true),
            Toggle::make('ativo')->label('Ativa')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')->label('Nome')->searchable()->sortable(),
                TextColumn::make('local.nome')->label('Local')->toggleable(),
                TextColumn::make('modalidade')->label('Modalidade')->badge(),
                TextColumn::make('frequencia')->label('Frequência')->badge(),
                TextColumn::make('dia_semana')->label('Dia')
                    ->formatStateUsing(fn ($state) => ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'][$state] ?? '-'),
                TextColumn::make('horario')->label('Horário')->time('H:i'),
                IconColumn::make('ativo')->label('Ativa')->boolean(),
            ])
            ->defaultSort('nome');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecorrencias::route('/'),
            'create' => Pages\CreateRecorrencia::route('/create'),
            'edit' => Pages\EditRecorrencia::route('/{record}/edit'),
        ];
    }
}
