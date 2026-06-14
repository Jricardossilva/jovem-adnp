<?php

namespace App\Models;

use App\Enums\FrequenciaRecorrencia;
use App\Enums\Modalidade;
use App\Enums\MetodoSorteio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recorrencia extends Model
{
    use SoftDeletes;

    protected $table = 'recorrencias';

    protected $fillable = [
        'nome',
        'local_id',
        'modalidade',
        'jogadores_por_time',
        'com_goleiro',
        'metodo_sorteio',
        'frequencia',
        'dia_semana',
        'horario',
        'exige_verificacao_telefone',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'modalidade' => Modalidade::class,
            'metodo_sorteio' => MetodoSorteio::class,
            'frequencia' => FrequenciaRecorrencia::class,
            'com_goleiro' => 'boolean',
            'exige_verificacao_telefone' => 'boolean',
            'ativo' => 'boolean',
            'jogadores_por_time' => 'integer',
            'dia_semana' => 'integer',
        ];
    }

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class);
    }

    public function peladas(): HasMany
    {
        return $this->hasMany(Pelada::class);
    }
}
