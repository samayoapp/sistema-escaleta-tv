<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Show extends Model
{
    protected $fillable = ['title', 'description', 'channel', 'status', 'production_type'];

    public function rundowns(): HasMany
    {
        return $this->hasMany(Rundown::class)->orderBy('air_date', 'desc');
    }

    // ── Helpers de tipo de producción ────────────────────────────────────────

    public function isLive(): bool
    {
        return $this->production_type === 'live';
    }

    public function isReality(): bool
    {
        return $this->production_type === 'reality';
    }

    public function productionLabel(): string
    {
        return \App\Config\SegmentTypes::TYPES[$this->production_type]['label']
            ?? ucfirst($this->production_type);
    }

    public function productionIcon(): string
    {
        return \App\Config\SegmentTypes::TYPES[$this->production_type]['icon'] ?? '📺';
    }
}
