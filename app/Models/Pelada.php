<?php

namespace App\Models;

use App\Enums\Modalidade;
use App\Enums\MetodoSorteio;
use App\Enums\StatusPelada;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Pelada extends Model
{
    use SoftDeletes;

    protected $table = 'peladas';

    protected $fillable = [
        'recorrencia_id',
        'local_id',
        'codigo',
        'modalidade',
        'jogadores_por_time',
        'com_goleiro',
        'metodo_sorteio',
        'data',
        'horario',
        'status',
        'exige_verificacao_telefone',
        'max_atletas',
        'observacoes',
        'sorteio_realizado_em',
        'encerrada_em',
        'fotos',
    ];

    protected function casts(): array
    {
        return [
            'modalidade' => Modalidade::class,
            'metodo_sorteio' => MetodoSorteio::class,
            'status' => StatusPelada::class,
            'data' => 'date',
            'com_goleiro' => 'boolean',
            'exige_verificacao_telefone' => 'boolean',
            'jogadores_por_time' => 'integer',
            'max_atletas' => 'integer',
            'sorteio_realizado_em' => 'datetime',
            'encerrada_em' => 'datetime',
            'fotos' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Pelada $pelada) {
            if (empty($pelada->codigo)) {
                $pelada->codigo = static::gerarCodigoUnico();
            }
        });
    }

    /** Gera um código curto, único e sem caracteres ambíguos (sem O/0/I/1). */
    public static function gerarCodigoUnico(int $tamanho = 6): string
    {
        $alfabeto = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $codigo = '';
            for ($i = 0; $i < $tamanho; $i++) {
                $codigo .= $alfabeto[random_int(0, strlen($alfabeto) - 1)];
            }
        } while (static::where('codigo', $codigo)->exists());

        return $codigo;
    }

    // ----------------------------------------------------------------
    // Relacionamentos
    // ----------------------------------------------------------------

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class);
    }

    public function recorrencia(): BelongsTo
    {
        return $this->belongsTo(Recorrencia::class);
    }

    public function inscricoes(): HasMany
    {
        return $this->hasMany(Inscricao::class);
    }

    public function times(): HasMany
    {
        return $this->hasMany(Time::class)->orderBy('ordem');
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    public function listaAberta(): bool
    {
        return $this->status === StatusPelada::Aberta;
    }

    public function totalPorTime(): int
    {
        return $this->jogadores_por_time + ($this->com_goleiro ? 1 : 0);
    }

    public function atletasPresentes()
    {
        return $this->inscricoes()->where('presente', true)->with('atleta')->get()
            ->pluck('atleta');
    }

    public function scopeDoDia(Builder $query, $data): Builder
    {
        return $query->whereDate('data', $data);
    }
}
