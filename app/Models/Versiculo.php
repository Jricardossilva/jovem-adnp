<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Versiculo extends Model
{
    protected $table = 'versiculos';

    protected $fillable = [
        'texto',
        'referencia',
        'tema',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    /** Sorteia um versículo ativo aleatório (ou null se não houver). */
    public static function aleatorio(): ?self
    {
        return static::query()->where('ativo', true)->inRandomOrder()->first();
    }
}
