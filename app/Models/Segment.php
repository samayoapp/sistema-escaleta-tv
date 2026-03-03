<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Segment extends Model
{
    protected $fillable = [
        'rundown_id', 
        'block_id', 
        'title', 
        'duration_seconds', 
        'script_content', 
        'order_index', 
        'type'
    ];

// Quita 'block_name' — ya no existe
    public function rundown(): BelongsTo
    {
        return $this->belongsTo(Rundown::class);
    }

    // ESTO FALTA: Conecta el segmento con su bloque
    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }
}