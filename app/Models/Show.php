<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Show extends Model
{
    protected $fillable = ['title', 'description', 'channel', 'status'];

    public function rundowns(): HasMany
    {
        return $this->hasMany(Rundown::class)->orderBy('air_date', 'desc');
    }
}