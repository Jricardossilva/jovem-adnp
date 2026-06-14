<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Time extends Model
{
    protected $table = 'times';

    protected $fillable = [
        'pelada_id',
        'nome',
        'cor',
        'ordem',
    ];

    public function pelada(): BelongsTo
    {
        return $this->belongsTo(Pelada::class);
    }

    public function atletas(): BelongsToMany
    {
        return $this->belongsToMany(Atleta::class, 'time_atleta')
            ->withPivot('e_goleiro')
            ->withTimestamps();
    }
}
