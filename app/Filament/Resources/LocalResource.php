<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocalResource\Pages;
use App\Models\Local;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LocalResource extends Resource
{
    protected static ?string $model = Local::class;

    protected static ?string $slug = 'locais';

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $modelLabel = 'local';

    protected static ?string $pluralModelLabel = 'locais';

    protected static ?string $navigationLabel = 'Locais';

    protected static ?string $navigationGroup = 'Cadastros';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nome')->label('Nome')->required()->maxLength(120),
            TextInput::make('endereco')->label('Endereço')->maxLength(255),
            Textarea::make('observacoes')->label('Observações')->rows(3),
            Toggle::make('ativo')->label('Ativo')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')->label('Nome')->searchable()->sortable(),
                TextColumn::make('endereco')->label('Endereço')->limit(40)->toggleable(),
                IconColumn::make('ativo')->label('Ativo')->boolean(),
                TextColumn::make('peladas_count')->counts('peladas')->label('Peladas'),
            ])
            ->defaultSort('nome');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocais::route('/'),
            'create' => Pages\CreateLocal::route('/create'),
            'edit' => Pages\EditLocal::route('/{record}/edit'),
        ];
    }
}
