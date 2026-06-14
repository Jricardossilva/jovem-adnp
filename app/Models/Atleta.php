<?php

namespace App\Models;

use App\Enums\SituacaoCadastro;
use App\Enums\StatusAtleta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Atleta extends Model
{
    use SoftDeletes;

    protected $table = 'atletas';

    protected $fillable = [
        'nome',
        'apelido',
        'telefone',
        'telefone_final',
        'e_goleiro',
        'nivel',
        'status',
        'situacao_cadastro',
        'faltas_consecutivas',
        'aprovado_em',
        'aprovado_por',
        'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'e_goleiro' => 'boolean',
            'nivel' => 'integer',
            'faltas_consecutivas' => 'integer',
            'status' => StatusAtleta::class,
            'situacao_cadastro' => SituacaoCadastro::class,
            'aprovado_em' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Mantém telefone_final sincronizado com os 4 últimos dígitos do telefone.
        static::saving(function (Atleta $atleta) {
            if ($atleta->isDirty('telefone')) {
                $digitos = preg_replace('/\D/', '', (string) $atleta->telefone);
                $atleta->telefone_final = $digitos ? substr($digitos, -4) : null;
            }
        });
    }

    // ----------------------------------------------------------------
    // Relacionamentos
    // ----------------------------------------------------------------

    public function inscricoes(): HasMany
    {
        return $this->hasMany(Inscricao::class);
    }

    public function suspensoes(): HasMany
    {
        return $this->hasMany(Suspensao::class);
    }

    public function aprovadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprovado_por');
    }

    public function times(): BelongsToMany
    {
        return $this->belongsToMany(Time::class, 'time_atleta')
            ->withPivot('e_goleiro')
            ->withTimestamps();
    }

    // ----------------------------------------------------------------
    // Regras de negócio
    // ----------------------------------------------------------------

    public function estaSuspenso(?Carbon $data = null): bool
    {
        $data ??= Carbon::today();

        return $this->suspensoes()
            ->whereDate('inicio', '<=', $data)
            ->whereDate('fim', '>=', $data)
            ->exists();
    }

    public function suspensaoAtiva(?Carbon $data = null): ?Suspensao
    {
        $data ??= Carbon::today();

        return $this->suspensoes()
            ->whereDate('inicio', '<=', $data)
            ->whereDate('fim', '>=', $data)
            ->orderByDesc('fim')
            ->first();
    }

    public function estaAprovado(): bool
    {
        return $this->situacao_cadastro === SituacaoCadastro::Aprovado;
    }

    /**
     * Pode entrar na lista / participar?
     * Precisa estar aprovado, com status Ativo e sem suspensão vigente.
     */
    public function podeParticipar(?Carbon $data = null): bool
    {
        return $this->estaAprovado()
            && $this->status === StatusAtleta::Ativo
            && ! $this->estaSuspenso($data);
    }

    public function motivoBloqueio(?Carbon $data = null): ?string
    {
        if (! $this->estaAprovado()) {
            return 'Cadastro ainda não aprovado pelo organizador.';
        }

        if ($this->status === StatusAtleta::Inativo) {
            return 'Atleta inativo. Procure o organizador para reativar.';
        }

        if ($this->estaSuspenso($data)) {
            $s = $this->suspensaoAtiva($data);
            $fim = $s ? $s->fim->format('d/m/Y') : null;

            return 'Atleta suspenso'.($fim ? " até $fim." : '.');
        }

        return null;
    }

    public function nomeExibicao(): string
    {
        return $this->apelido ? "{$this->nome} ({$this->apelido})" : $this->nome;
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeAtivos(Builder $query): Builder
    {
        return $query->where('status', StatusAtleta::Ativo->value);
    }

    public function scopeAprovados(Builder $query): Builder
    {
        return $query->where('situacao_cadastro', SituacaoCadastro::Aprovado->value);
    }

    public function scopePendentes(Builder $query): Builder
    {
        return $query->where('situacao_cadastro', SituacaoCadastro::Pendente->value);
    }
}
