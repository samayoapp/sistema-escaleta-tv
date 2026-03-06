<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Segment extends Model
{
    protected $fillable = [
    'rundown_id', 
    'block_id', 
    'order_index',
    'title', 
    'duration_seconds', 
    'type',
    'script_content', 
    'has_script'
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