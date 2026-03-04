<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rundown extends Model
{
    // Esto permite que el Seeder guarde datos sin errores
    protected $fillable = ['show_id', 'air_date', 'air_time', 'status'];

    // Relación: Una escaleta PERTENECE A un programa
    public function show(): BelongsTo
    {
        return $this->belongsTo(Show::class);
    }

    // Relación: Una escaleta TIENE MUCHOS segmentos
    public function segments(): HasMany
    {
        return $this->hasMany(Segment::class)->orderBy('order_index');
    }

    public function blocks()
    {
        return $this->hasMany(Block::class);
    }
}