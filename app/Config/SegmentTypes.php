<?php

namespace App\Config;

/**
 * Catálogo de tipos de segmento.
 * Lee de la tabla `segment_types` en BD.
 * Si la tabla no existe aún (primera instalación), usa el array de fallback.
 */
class SegmentTypes
{
    public const TYPES = [
        'type_1' => 'Nombre del tipo 1',
        'type_2' => 'Nombre del tipo 2',
    ];


    // ── Fallback estático (solo si la BD no está disponible) ──────────────────
    const FALLBACK = [
        'live' => [
            'label' => 'Programa en Vivo',
            'icon'  => '📡',
            'segments' => [
                ['value' => 'VIVO',            'label' => 'VIVO',         'icon' => '🔴', 'color' => 'text-red-400',    'badge_bg' => 'bg-red-900/30',    'border' => '#ef4444'],
                ['value' => 'VTR',             'label' => 'VTR',          'icon' => '🎬', 'color' => 'text-green-400',  'badge_bg' => 'bg-green-900/30',  'border' => '#22c55e'],
                ['value' => 'OFF',             'label' => 'OFF',          'icon' => '🎙️', 'color' => 'text-purple-400', 'badge_bg' => 'bg-purple-900/30', 'border' => '#a855f7'],
                ['value' => 'CORTE_COMERCIAL', 'label' => 'COMERCIAL',    'icon' => '💰', 'color' => 'text-yellow-400', 'badge_bg' => 'bg-yellow-900/30', 'border' => '#eab308'],
                ['value' => 'NOTA_SECA',       'label' => 'NOTA SECA',    'icon' => '📄', 'color' => 'text-gray-400',   'badge_bg' => 'bg-gray-700/30',   'border' => '#94a3b8'],
                ['value' => 'PRESENTACION',    'label' => 'PRESENTACIÓN', 'icon' => '🎤', 'color' => 'text-blue-400',   'badge_bg' => 'bg-blue-900/30',   'border' => '#3b82f6'],
                ['value' => 'CIERRE',          'label' => 'CIERRE',       'icon' => '🏁', 'color' => 'text-orange-400', 'badge_bg' => 'bg-orange-900/30', 'border' => '#f97316'],
            ],
        ],
        'reality' => [
            'label' => 'Reality de TV',
            'icon'  => '🎬',
            'segments' => [
                ['value' => 'EN_CAMARA',        'label' => 'EN CÁMARA',     'icon' => '📹', 'color' => 'text-red-400',    'badge_bg' => 'bg-red-900/30',    'border' => '#ef4444'],
                ['value' => 'CONFESIONARIO',    'label' => 'CONFESIONARIO', 'icon' => '🗣️', 'color' => 'text-pink-400',   'badge_bg' => 'bg-pink-900/30',   'border' => '#ec4899'],
                ['value' => 'MATERIAL_ARCHIVO', 'label' => 'ARCHIVO',       'icon' => '🗃️', 'color' => 'text-green-400',  'badge_bg' => 'bg-green-900/30',  'border' => '#22c55e'],
                ['value' => 'NARRACION_OFF',    'label' => 'NARRACIÓN',     'icon' => '🎙️', 'color' => 'text-purple-400', 'badge_bg' => 'bg-purple-900/30', 'border' => '#a855f7'],
                ['value' => 'CORTE_COMERCIAL',  'label' => 'COMERCIAL',     'icon' => '💰', 'color' => 'text-yellow-400', 'badge_bg' => 'bg-yellow-900/30', 'border' => '#eab308'],
                ['value' => 'RETO',             'label' => 'RETO / PRUEBA', 'icon' => '⚡', 'color' => 'text-orange-400', 'badge_bg' => 'bg-orange-900/30', 'border' => '#f97316'],
                ['value' => 'ELIMINACION',      'label' => 'ELIMINACIÓN',   'icon' => '🚪', 'color' => 'text-red-600',    'badge_bg' => 'bg-red-950/50',    'border' => '#dc2626'],
                ['value' => 'TRANSICION',       'label' => 'TRANSICIÓN',    'icon' => '⏭️', 'color' => 'text-gray-400',   'badge_bg' => 'bg-gray-700/30',   'border' => '#94a3b8'],
                ['value' => 'CIERRE',           'label' => 'CIERRE',        'icon' => '🏁', 'color' => 'text-blue-400',   'badge_bg' => 'bg-blue-900/30',   'border' => '#3b82f6'],
            ],
        ],
    ];

    // ── Cache en memoria por request ──────────────────────────────────────────
    private static array $_cache = [];

    /**
     * Retorna los tipos de segmento para un production_type dado.
     * Lee de BD; fallback al array si la tabla no existe.
     */
    public static function forType(string $productionType): array
    {
        if (isset(self::$_cache[$productionType])) {
            return self::$_cache[$productionType];
        }

        try {
            $rows = \App\Models\SegmentType::active()
                ->forType($productionType)
                ->orderBy('order_index')
                ->get();

            if ($rows->isEmpty()) {
                // Sin datos en BD → fallback
                return self::FALLBACK[$productionType]['segments']
                    ?? self::FALLBACK['live']['segments'];
            }

            $result = $rows->map(fn($r) => [
                'value'    => $r->value,
                'label'    => $r->label,
                'icon'     => $r->icon,
                'color'    => $r->tailwindTextColor(),
                'badge_bg' => 'bg-gray-700/30',
                'border'   => $r->color_hex,
            ])->toArray();

            self::$_cache[$productionType] = $result;
            return $result;

        } catch (\Exception $e) {
            // Tabla no existe todavía → fallback
            return self::FALLBACK[$productionType]['segments']
                ?? self::FALLBACK['live']['segments'];
        }
    }

    public static function valuesForType(string $productionType): array
    {
        return array_column(self::forType($productionType), 'value');
    }

    public static function label(string $productionType, string $value): string
    {
        foreach (self::forType($productionType) as $type) {
            if ($type['value'] === $value) return $type['label'];
        }
        return $value;
    }

    public static function productionTypes(): array
    {
        // Tipos de producción disponibles — por ahora estáticos
        // En el futuro vendrán de BD también
        return [
            ['value' => 'live',    'label' => 'Programa en Vivo', 'icon' => '📡'],
            ['value' => 'reality', 'label' => 'Reality de TV',    'icon' => '🎬'],
        ];
    }
}
