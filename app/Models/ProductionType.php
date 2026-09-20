<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductionType extends Model
{
    protected $fillable = [
        'value',
        'label',
        'icon',
        'has_air_time',
        'has_lock',
        'has_episode',
        'order_index',
        'active',
    ];

    protected $casts = [
        'has_air_time' => 'boolean',
        'has_lock'     => 'boolean',
        'has_episode'  => 'boolean',
        'active'       => 'boolean',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function segmentTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            SegmentType::class,
            'production_type_segment_type'
        )->withPivot('active', 'order_index')->withTimestamps();
    }

    public function activeSegmentTypes(): BelongsToMany
    {
        return $this->segmentTypes()
            ->wherePivot('active', true)
            ->orderByPivot('order_index');
    }

    public function shows()
    {
        return $this->hasMany(Show::class, 'production_type', 'value');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('active', true)->orderBy('order_index');
    }

    // ── Helpers estáticos ────────────────────────────────────────────────────

    /**
     * Obtener un ProductionType por su value string.
     * Con cache en memoria para evitar múltiples queries por request.
     */
    private static array $_cache = [];

    public static function findByValue(string $value): ?self
    {
        if (!isset(self::$_cache[$value])) {
            self::$_cache[$value] = self::where('value', $value)->first();
        }
        return self::$_cache[$value];
    }

    public static function clearCache(): void
    {
        self::$_cache = [];
    }

    // ── Helpers de instancia ─────────────────────────────────────────────────

    public function hasAirTime(): bool   { return (bool) $this->has_air_time; }
    public function hasLock(): bool      { return (bool) $this->has_lock; }
    public function hasEpisode(): bool   { return (bool) $this->has_episode; }
}
