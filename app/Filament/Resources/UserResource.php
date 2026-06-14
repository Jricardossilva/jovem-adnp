<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = 'organizadores';

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $modelLabel = 'organizador';

    protected static ?string $pluralModelLabel = 'organizadores';

    protected static ?string $navigationLabel = 'Organizadores';

    protected static ?string $navigationGroup = 'Sistema';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->label('Nome')->required()->maxLength(120),
            TextInput::make('email')->label('E-mail')->email()->required()
                ->unique(ignoreRecord: true)->maxLength(160),
            TextInput::make('password')->label('Senha')->password()->revealable()
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $operation) => $operation === 'create')
                ->minLength(6)
                ->helperText('Ao editar, deixe em branco para manter a senha atual.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nome')->searchable()->sortable(),
                TextColumn::make('email')->label('E-mail')->searchable()->sortable(),
                TextColumn::make('created_at')->label('Criado em')->dateTime('d/m/Y H:i')->toggleable(),
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}