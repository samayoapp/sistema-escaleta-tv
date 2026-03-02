<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Segment extends Model
{
    protected $fillable = ['rundown_id', 'order_index', 'title', 'duration_seconds', 'script_content'];

    public function rundown(): BelongsTo
    {
        return $this->belongsTo(Rundown::class);
    }
}