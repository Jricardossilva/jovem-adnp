<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Local extends Model
{
    use SoftDeletes;

    protected $table = 'locais';

    protected $fillable = [
        'nome',
        'endereco',
        'observacoes',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    public function peladas(): HasMany
    {
        return $this->hasMany(Pelada::class);
    }

    public function recorrencias(): HasMany
    {
        return $this->hasMany(Recorrencia::class);
    }
}
