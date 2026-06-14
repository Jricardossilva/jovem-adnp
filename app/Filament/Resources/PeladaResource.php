<?php

namespace App\Filament\Resources;

use App\Enums\Modalidade;
use App\Enums\MetodoSorteio;
use App\Enums\StatusPelada;
use App\Filament\Resources\PeladaResource\Pages;
use App\Filament\Resources\PeladaResource\RelationManagers\InscricoesRelationManager;
use App\Filament\Resources\PeladaResource\RelationManagers\TimesRelationManager;
use App\Models\Pelada;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PeladaResource extends Resource
{
    protected static ?string $model = Pelada::class;

    protected static ?string $slug = 'peladas';

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $modelLabel = 'pelada';

    protected static ?string $pluralModelLabel = 'peladas';

    protected static ?string $navigationLabel = 'Peladas';

    protected static ?string $navigationGroup = 'Peladas';

    protected static ?int $navigationSort = -1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Dados da pelada')->schema([
                Select::make('local_id')->label('Local')->relationship('local', 'nome')
                    ->required()->searchable()->preload(),
                Select::make('recorrencia_id')->label('Recorrência (opcional)')
                    ->relationship('recorrencia', 'nome')->searchable()->preload(),
                Select::make('modalidade')->label('Modalidade')
                    ->options(Modalidade::class)->default(Modalidade::Futsal)->required()->live()
                    ->afterStateUpdated(function ($state, Set $set) {
                        if ($state) {
                            $m = $state instanceof Modalidade ? $state : Modalidade::from($state);
                            $set('jogadores_por_time', $m->jogadoresPorTimePadrao());
                            $set('com_goleiro', $m->usaGoleiroPadrao());
                        }
                    }),
                TextInput::make('jogadores_por_time')->label('Jogadores de linha por time')
                    ->numeric()->minValue(2)->maxValue(11)->default(4)->required(),
                Toggle::make('com_goleiro')->label('Tem goleiro?')->default(true),
                Select::make('metodo_sorteio')->label('Método de sorteio')
                    ->options(MetodoSorteio::class)->default(MetodoSorteio::Aleatorio)->required(),
                DatePicker::make('data')->label('Data')->default(now())->required(),
                TimePicker::make('horario')->label('Horário')->seconds(false)->required(),
                Select::make('status')->label('Status')->options(StatusPelada::class)
                    ->default(StatusPelada::Aberta)->required()
                    ->helperText('Use "Lista aberta" para liberar a inscrição dos atletas.'),
                Toggle::make('exige_verificacao_telefone')->label('Exigir verificação por telefone')->default(true),
                TextInput::make('max_atletas')->label('Limite de atletas (opcional)')->numeric()->minValue(2),
                Textarea::make('observacoes')->label('Observações')->rows(2)->columnSpanFull(),
            ])->columns(2),

            Group::make([
                Placeholder::make('codigo_view')->label('Código da pelada')
                    ->content(fn (?Pelada $record) => $record?->codigo ?? 'Gerado automaticamente ao salvar')
                    ->extraAttributes(['class' => 'text-lg font-bold tracking-widest']),
            ])->visibleOn('edit'),

            Section::make('Fotos da participação')
                ->description('Comprovação dos atletas presentes no dia.')
                ->schema([
                    FileUpload::make('fotos')
                        ->label('Fotos')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->disk('public')
                        ->directory('peladas/fotos')
                        ->visibility('public')
                        ->maxSize(8192) // 8 MB por foto
                        ->maxFiles(30)
                        ->downloadable()
                        ->openable(),
                ])->visibleOn('edit'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('data')->label('Data')->date('d/m/Y')->sortable(),
                TextColumn::make('horario')->label('Hora')->time('H:i'),
                TextColumn::make('local.nome')->label('Local')->toggleable(),
                TextColumn::make('codigo')->label('Código')->badge()->color('gray')->copyable(),
                TextColumn::make('modalidade')->label('Modalidade')->badge()->toggleable(),
                TextColumn::make('inscricoes_count')->counts('inscricoes')->label('Inscritos'),
                TextColumn::make('presentes')->label('Presentes')
                    ->getStateUsing(fn (Pelada $r) => $r->inscricoes()->where('presente', true)->count()),
                TextColumn::make('status')->label('Status')->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Status')->options(StatusPelada::class),
                SelectFilter::make('local_id')->label('Local')->relationship('local', 'nome'),
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make()->label('Gerenciar'),
            ])
            ->defaultSort('data', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            InscricoesRelationManager::class,
            TimesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeladas::route('/'),
            'create' => Pages\CreatePelada::route('/create'),
            'edit' => Pages\EditPelada::route('/{record}/edit'),
        ];
    }
}
