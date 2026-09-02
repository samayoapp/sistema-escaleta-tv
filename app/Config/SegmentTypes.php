<?php

namespace App\Config;

/**
 * Catálogo central de tipos de segmento por tipo de producción.
 *
 * Para agregar un nuevo production_type en el futuro:
 *   1. Agregar una entrada en TYPES con clave = production_type
 *   2. Definir sus tipos con: value, label, color (Tailwind), border_color (hex para PDF)
 *   3. El sistema lo toma automáticamente en el selector y en los PDFs.
 */
class SegmentTypes
{
    /**
     * Todos los tipos disponibles agrupados por production_type.
     */
    const TYPES = [

        // ── PROGRAMA EN VIVO ──────────────────────────────────────────────
        'live' => [
            'label' => 'Programa en Vivo',
            'icon'  => '📡',
            'segments' => [
                ['value' => 'VIVO',            'label' => 'VIVO',         'color' => 'text-red-400',    'badge_bg' => 'bg-red-900/30',    'border' => '#ef4444'],
                ['value' => 'VTR',             'label' => 'VTR',          'color' => 'text-green-400',  'badge_bg' => 'bg-green-900/30',  'border' => '#22c55e'],
                ['value' => 'OFF',             'label' => 'OFF',          'color' => 'text-purple-400', 'badge_bg' => 'bg-purple-900/30', 'border' => '#a855f7'],
                ['value' => 'CORTE_COMERCIAL', 'label' => 'COMERCIAL',    'color' => 'text-yellow-400', 'badge_bg' => 'bg-yellow-900/30', 'border' => '#eab308'],
                ['value' => 'NOTA_SECA',       'label' => 'NOTA SECA',    'color' => 'text-gray-400',   'badge_bg' => 'bg-gray-700/30',   'border' => '#94a3b8'],
                ['value' => 'PRESENTACION',    'label' => 'PRESENTACIÓN', 'color' => 'text-blue-400',   'badge_bg' => 'bg-blue-900/30',   'border' => '#3b82f6'],
                ['value' => 'CIERRE',          'label' => 'CIERRE',       'color' => 'text-orange-400', 'badge_bg' => 'bg-orange-900/30', 'border' => '#f97316'],
            ],
        ],

        // ── REALITY DE TV ────────────────────────────────────────────────
        'reality' => [
            'label' => 'Reality de TV',
            'icon'  => '🎬',
            'segments' => [
                ['value' => 'EN_CAMARA',        'label' => 'EN CÁMARA',        'color' => 'text-red-400',    'badge_bg' => 'bg-red-900/30',    'border' => '#ef4444'],
                ['value' => 'CONFESIONARIO',    'label' => 'CONFESIONARIO',    'color' => 'text-pink-400',   'badge_bg' => 'bg-pink-900/30',   'border' => '#ec4899'],
                ['value' => 'MATERIAL_ARCHIVO', 'label' => 'ARCHIVO',          'color' => 'text-green-400',  'badge_bg' => 'bg-green-900/30',  'border' => '#22c55e'],
                ['value' => 'NARRACION_OFF',    'label' => 'NARRACIÓN',        'color' => 'text-purple-400', 'badge_bg' => 'bg-purple-900/30', 'border' => '#a855f7'],
                ['value' => 'CORTE_COMERCIAL',  'label' => 'COMERCIAL',        'color' => 'text-yellow-400', 'badge_bg' => 'bg-yellow-900/30', 'border' => '#eab308'],
                ['value' => 'RETO',             'label' => 'RETO / PRUEBA',    'color' => 'text-orange-400', 'badge_bg' => 'bg-orange-900/30', 'border' => '#f97316'],
                ['value' => 'ELIMINACION',      'label' => 'ELIMINACIÓN',      'color' => 'text-red-600',    'badge_bg' => 'bg-red-950/50',    'border' => '#dc2626'],
                ['value' => 'TRANSICION',       'label' => 'TRANSICIÓN',       'color' => 'text-gray-400',   'badge_bg' => 'bg-gray-700/30',   'border' => '#94a3b8'],
                ['value' => 'CIERRE',           'label' => 'CIERRE',           'color' => 'text-blue-400',   'badge_bg' => 'bg-blue-900/30',   'border' => '#3b82f6'],
            ],
        ],

        // ── PRÓXIMOS — descomentar y completar cuando se necesiten ────────
        // 'documentary' => [ 'label' => 'Documental', 'icon' => '🎥', 'segments' => [...] ],
        // 'talk_show'   => [ 'label' => 'Talk Show',  'icon' => '🎤', 'segments' => [...] ],
        // 'news'        => [ 'label' => 'Noticiero',  'icon' => '📰', 'segments' => [...] ],

    ];

    /**
     * Retorna los tipos de segmento para un production_type dado.
     * Si no existe, devuelve los de 'live' como fallback.
     */
    public static function forType(string $productionType): array
    {
        return self::TYPES[$productionType]['segments'] ?? self::TYPES['live']['segments'];
    }

    /**
     * Retorna solo los values (para validación en el controller).
     */
    public static function valuesForType(string $productionType): array
    {
        return array_column(self::forType($productionType), 'value');
    }

    /**
     * Retorna el label de un value específico para un production_type.
     */
    public static function label(string $productionType, string $value): string
    {
        foreach (self::forType($productionType) as $type) {
            if ($type['value'] === $value) return $type['label'];
        }
        return $value;
    }

    /**
     * Retorna todos los production_types disponibles (para el selector de shows).
     */
    public static function productionTypes(): array
    {
        return array_map(fn($key, $val) => [
            'value' => $key,
            'label' => $val['label'],
            'icon'  => $val['icon'],
        ], array_keys(self::TYPES), self::TYPES);
    }
}
