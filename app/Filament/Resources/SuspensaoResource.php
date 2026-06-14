<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuspensaoResource\Pages;
use App\Models\Suspensao;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SuspensaoResource extends Resource
{
    protected static ?string $model = Suspensao::class;

    protected static ?string $slug = 'suspensoes';

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';

    protected static ?string $modelLabel = 'suspensão';

    protected static ?string $pluralModelLabel = 'suspensões';

    protected static ?string $navigationLabel = 'Suspensões';

    protected static ?string $navigationGroup = 'Cadastros';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('atleta_id')->label('Atleta')->relationship('atleta', 'nome')
                ->required()->searchable()->preload(),
            DatePicker::make('inicio')->label('Início')->default(now())->required(),
            DatePicker::make('fim')->label('Fim')->required()->after('inicio'),
            Textarea::make('motivo')->label('Motivo')->required()->rows(3)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('atleta.nome')->label('Atleta')->searchable()->sortable(),
                TextColumn::make('inicio')->label('Início')->date('d/m/Y')->sortable(),
                TextColumn::make('fim')->label('Fim')->date('d/m/Y')->sortable(),
                IconColumn::make('vigente')->label('Vigente')
                    ->getStateUsing(fn (Suspensao $r) => $r->vigente())->boolean(),
                TextColumn::make('motivo')->label('Motivo')->limit(40)->wrap(),
                TextColumn::make('criadoPor.name')->label('Por')->toggleable(),
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('inicio', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuspensoes::route('/'),
            'create' => Pages\CreateSuspensao::route('/create'),
            'edit' => Pages\EditSuspensao::route('/{record}/edit'),
        ];
    }
}
