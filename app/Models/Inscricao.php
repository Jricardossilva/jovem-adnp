<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inscricao extends Model
{
    protected $table = 'inscricoes';

    protected $fillable = [
        'pelada_id',
        'atleta_id',
        'presente',
        'confirmado_em',
        'origem',
    ];

    protected function casts(): array
    {
        return [
            'presente' => 'boolean',
            'confirmado_em' => 'datetime',
        ];
    }

    public function pelada(): BelongsTo
    {
        return $this->belongsTo(Pelada::class);
    }

    public function atleta(): BelongsTo
    {
        return $this->belongsTo(Atleta::class);
    }
}
