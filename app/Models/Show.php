<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Show extends Model
{
    protected $fillable = ['title', 'description', 'channel', 'status', 'production_type'];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function rundowns(): HasMany
    {
        return $this->hasMany(Rundown::class)->orderBy('air_date', 'desc');
    }

    /**
     * Relación al modelo ProductionType via la columna string 'production_type'.
     * No es una FK real en BD — usamos belongsTo con localKey/foreignKey custom.
     */
    public function productionTypeModel(): BelongsTo
    {
        return $this->belongsTo(ProductionType::class, 'production_type', 'value');
    }

    // ── Helpers que leen condiciones de BD ───────────────────────────────────

    private ?ProductionType $_ptCache = null;

    private function getPT(): ?ProductionType
    {
        if ($this->_ptCache === null) {
            $this->_ptCache = ProductionType::findByValue($this->production_type ?? 'live');
        }
        return $this->_ptCache;
    }

    public function isLive(): bool
    {
        // Fallback: si no hay registro en BD, usar la lógica anterior
        $pt = $this->getPT();
        if (!$pt) return $this->production_type === 'live';
        return $pt->hasAirTime() && $pt->hasLock();
    }

    public function isReality(): bool
    {
        $pt = $this->getPT();
        if (!$pt) return $this->production_type === 'reality';
        return $pt->hasEpisode() && !$pt->hasLock();
    }

    public function hasAirTime(): bool
    {
        return $this->getPT()?->hasAirTime() ?? ($this->production_type === 'live');
    }

    public function hasLock(): bool
    {
        return $this->getPT()?->hasLock() ?? ($this->production_type === 'live');
    }

    public function hasEpisode(): bool
    {
        return $this->getPT()?->hasEpisode() ?? ($this->production_type === 'reality');
    }

    public function productionLabel(): string
    {
        return $this->getPT()?->label ?? ucfirst($this->production_type ?? '');
    }

    public function productionIcon(): string
    {
        return $this->getPT()?->icon ?? '📺';
    }
}
