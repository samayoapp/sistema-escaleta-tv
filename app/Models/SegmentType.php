<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SegmentType extends Model
{
    protected $fillable = [
        'value',
        'label',
        'icon',
        'color_hex',
        'order_index',
        'active',
        // production_type queda en la tabla por compatibilidad
        // pero ya no se usa para la lógica principal
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function productionTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductionType::class,
            'production_type_segment_type'
        )->withPivot('active', 'order_index')->withTimestamps();
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * @deprecated Usar la relación via ProductionType en su lugar.
     * Se mantiene por compatibilidad con código existente.
     */
    public function scopeForType($query, string $productionType)
    {
        return $query->where('production_type', $productionType);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function tailwindTextColor(): string
    {
        $map = [
            '#ef4444' => 'text-red-400',
            '#dc2626' => 'text-red-600',
            '#22c55e' => 'text-green-400',
            '#a855f7' => 'text-purple-400',
            '#eab308' => 'text-yellow-400',
            '#94a3b8' => 'text-gray-400',
            '#3b82f6' => 'text-blue-400',
            '#f97316' => 'text-orange-400',
            '#ec4899' => 'text-pink-400',
        ];
        return $map[$this->color_hex] ?? 'text-gray-400';
    }
}
