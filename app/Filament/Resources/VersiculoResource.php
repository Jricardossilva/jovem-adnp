<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VersiculoResource\Pages;
use App\Models\Versiculo;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VersiculoResource extends Resource
{
    protected static ?string $model = Versiculo::class;

    protected static ?string $slug = 'versiculos';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $modelLabel = 'versículo';

    protected static ?string $pluralModelLabel = 'versículos';

    protected static ?string $navigationLabel = 'Versículos';

    protected static ?string $navigationGroup = 'Cadastros';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Textarea::make('texto')->label('Texto')->required()->rows(3)->columnSpanFull(),
            TextInput::make('referencia')->label('Referência')->required()->placeholder('Ex.: Eclesiastes 4:9')->maxLength(120),
            TextInput::make('tema')->label('Tema')->placeholder('união, disciplina, respeito...')->maxLength(60),
            Toggle::make('ativo')->label('Ativo')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('referencia')->label('Referência')->searchable()->sortable(),
                TextColumn::make('texto')->label('Texto')->limit(60)->wrap(),
                TextColumn::make('tema')->label('Tema')->badge()->toggleable(),
                IconColumn::make('ativo')->label('Ativo')->boolean(),
            ])
            ->defaultSort('referencia');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVersiculos::route('/'),
            'create' => Pages\CreateVersiculo::route('/create'),
            'edit' => Pages\EditVersiculo::route('/{record}/edit'),
        ];
    }
}
