<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SegmentType extends Model
{
    protected $fillable = [
        'production_type',
        'value',
        'label',
        'icon',
        'color_hex',
        'order_index',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeForType($query, string $productionType)
    {
        return $query->where('production_type', $productionType);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Convierte el color hex a clases Tailwind aproximadas para la UI.
     * Para la tabla y el editor usamos el hex directamente en style="".
     */
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
