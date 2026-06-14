<?php

namespace App\Filament\Resources;

use Filament\Support\RawJs;
use App\Enums\SituacaoCadastro;
use App\Enums\StatusAtleta;
use App\Filament\Resources\AtletaResource\Pages;
use App\Models\Atleta;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AtletaResource extends Resource
{
    protected static ?string $model = Atleta::class;

    protected static ?string $slug = 'atletas';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'atleta';

    protected static ?string $pluralModelLabel = 'atletas';

    protected static ?string $navigationLabel = 'Atletas';

    protected static ?string $navigationGroup = 'Cadastros';

    public static function getNavigationBadge(): ?string
    {
        $pendentes = Atleta::pendentes()->count();

        return $pendentes > 0 ? (string) $pendentes : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nome')->label('Nome')->required()->maxLength(120),
            TextInput::make('apelido')->label('Apelido')->maxLength(60),
            TextInput::make('telefone')->label('Telefone')->tel()->maxLength(20)
            ->mask(RawJs::make(<<<'JS'
                $input.length > 14 ? '(99) 99999-9999' : '(99) 9999-9999'
            JS))
            ->helperText('Os 4 últimos dígitos são usados na verificação de acesso.'),
            Toggle::make('e_goleiro')->label('É goleiro?'),
            Select::make('nivel')->label('Nível')
                ->options([1 => '1 - Iniciante', 2 => '2', 3 => '3 - Médio', 4 => '4', 5 => '5 - Avançado'])
                ->default(3)->required(),
            Select::make('situacao_cadastro')->label('Situação do cadastro')
                ->options(SituacaoCadastro::class)->default(SituacaoCadastro::Aprovado)->required(),
            Select::make('status')->label('Status')
                ->options(StatusAtleta::class)->default(StatusAtleta::Ativo)->required(),
            Textarea::make('observacoes')->label('Observações')->rows(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')->label('Nome')->searchable()->sortable()
                    ->description(fn (Atleta $r) => $r->apelido),
                TextColumn::make('e_goleiro')->label('Goleiro')
                    ->formatStateUsing(fn ($state) => $state ? 'Goleiro' : 'Linha')
                    ->badge()->color(fn ($state) => $state ? 'info' : 'gray'),
                TextColumn::make('nivel')->label('Nível')->badge()->sortable(),
                TextColumn::make('situacao_cadastro')->label('Cadastro')->badge(),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('faltas_consecutivas')->label('Faltas seg.')->badge()
                    ->color(fn ($state) => $state >= 4 ? 'danger' : ($state >= 2 ? 'warning' : 'gray'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Status')->options(StatusAtleta::class),
                SelectFilter::make('situacao_cadastro')->label('Cadastro')->options(SituacaoCadastro::class),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('aprovar')
                        ->label('Aprovar')->icon('heroicon-o-check-circle')->color('success')
                        ->visible(fn (Atleta $r) => $r->situacao_cadastro !== SituacaoCadastro::Aprovado)
                        ->requiresConfirmation()
                        ->action(function (Atleta $r) {
                            $r->update([
                                'situacao_cadastro' => SituacaoCadastro::Aprovado->value,
                                'aprovado_em' => now(),
                                'aprovado_por' => auth()->id(),
                            ]);
                        }),
                    Action::make('reativar')
                        ->label('Reativar')->icon('heroicon-o-arrow-path')->color('success')
                        ->visible(fn (Atleta $r) => $r->status === StatusAtleta::Inativo)
                        ->requiresConfirmation()
                        ->action(fn (Atleta $r) => $r->update([
                            'status' => StatusAtleta::Ativo->value,
                            'faltas_consecutivas' => 0,
                        ])),
                    Action::make('inativar')
                        ->label('Inativar')->icon('heroicon-o-no-symbol')->color('gray')
                        ->visible(fn (Atleta $r) => $r->status === StatusAtleta::Ativo)
                        ->requiresConfirmation()
                        ->action(fn (Atleta $r) => $r->update(['status' => StatusAtleta::Inativo->value])),
                    Action::make('suspender')
                        ->label('Suspender')->icon('heroicon-o-hand-raised')->color('danger')
                        ->form([
                            DatePicker::make('inicio')->label('Início')->default(now())->required(),
                            DatePicker::make('fim')->label('Fim')->required()->after('inicio'),
                            Textarea::make('motivo')->label('Motivo')->required()->rows(2),
                        ])
                        ->action(function (Atleta $r, array $data) {
                            $r->suspensoes()->create([
                                'inicio' => $data['inicio'],
                                'fim' => $data['fim'],
                                'motivo' => $data['motivo'],
                                'criado_por' => auth()->id(),
                            ]);
                            if ($r->estaSuspenso()) {
                                $r->update(['status' => StatusAtleta::Suspenso->value]);
                            }
                        }),
                    EditAction::make(),
                ]),
            ])
            ->defaultSort('nome');
    }

    public static function getRelations(): array
    {
        return [
            AtletaResource\RelationManagers\SuspensoesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAtletas::route('/'),
            'create' => Pages\CreateAtleta::route('/create'),
            'edit' => Pages\EditAtleta::route('/{record}/edit'),
        ];
    }
}
