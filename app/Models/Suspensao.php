<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Suspensao extends Model
{
    use SoftDeletes;

    protected $table = 'suspensoes';

    protected $fillable = [
        'atleta_id',
        'motivo',
        'inicio',
        'fim',
        'criado_por',
    ];

    protected function casts(): array
    {
        return [
            'inicio' => 'date',
            'fim' => 'date',
        ];
    }

    public function atleta(): BelongsTo
    {
        return $this->belongsTo(Atleta::class);
    }

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function vigente(?Carbon $data = null): bool
    {
        $data ??= Carbon::today();

        return $this->inicio->lte($data) && $this->fim->gte($data);
    }

    public function scopeVigentes(Builder $query, ?Carbon $data = null): Builder
    {
        $data ??= Carbon::today();

        return $query->whereDate('inicio', '<=', $data)
            ->whereDate('fim', '>=', $data);
    }
}
