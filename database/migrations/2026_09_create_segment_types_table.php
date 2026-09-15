<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('segment_types', function (Blueprint $table) {
            $table->id();
            $table->string('production_type');
            $table->string('value');
            $table->string('label');
            $table->string('icon')->default('📄');
            $table->string('color_hex')->default('#94a3b8');
            $table->integer('order_index')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();

            // Único por combinación production_type + value
            $table->unique(['production_type', 'value']);
        });

        $tipos = [
            // ── LIVE ──────────────────────────────────────────────────────
            ['production_type' => 'live', 'value' => 'VIVO',            'label' => 'VIVO',         'icon' => '🔴', 'color_hex' => '#ef4444', 'order_index' => 1],
            ['production_type' => 'live', 'value' => 'VTR',             'label' => 'VTR',          'icon' => '🎬', 'color_hex' => '#22c55e', 'order_index' => 2],
            ['production_type' => 'live', 'value' => 'OFF',             'label' => 'OFF',          'icon' => '🎙️', 'color_hex' => '#a855f7', 'order_index' => 3],
            ['production_type' => 'live', 'value' => 'CORTE_COMERCIAL', 'label' => 'COMERCIAL',    'icon' => '💰', 'color_hex' => '#eab308', 'order_index' => 4],
            ['production_type' => 'live', 'value' => 'NOTA_SECA',       'label' => 'NOTA SECA',    'icon' => '📄', 'color_hex' => '#94a3b8', 'order_index' => 5],
            ['production_type' => 'live', 'value' => 'PRESENTACION',    'label' => 'PRESENTACIÓN', 'icon' => '🎤', 'color_hex' => '#3b82f6', 'order_index' => 6],
            ['production_type' => 'live', 'value' => 'CIERRE',          'label' => 'CIERRE',       'icon' => '🏁', 'color_hex' => '#f97316', 'order_index' => 7],

            // ── REALITY ───────────────────────────────────────────────────
            ['production_type' => 'reality', 'value' => 'EN_CAMARA',        'label' => 'EN CÁMARA',     'icon' => '📹', 'color_hex' => '#ef4444', 'order_index' => 1],
            ['production_type' => 'reality', 'value' => 'CONFESIONARIO',    'label' => 'CONFESIONARIO', 'icon' => '🗣️', 'color_hex' => '#ec4899', 'order_index' => 2],
            ['production_type' => 'reality', 'value' => 'MATERIAL_ARCHIVO', 'label' => 'ARCHIVO',       'icon' => '🗃️', 'color_hex' => '#22c55e', 'order_index' => 3],
            ['production_type' => 'reality', 'value' => 'NARRACION_OFF',    'label' => 'NARRACIÓN',     'icon' => '🎙️', 'color_hex' => '#a855f7', 'order_index' => 4],
            ['production_type' => 'reality', 'value' => 'CORTE_COMERCIAL',  'label' => 'COMERCIAL',     'icon' => '💰', 'color_hex' => '#eab308', 'order_index' => 5],
            ['production_type' => 'reality', 'value' => 'RETO',             'label' => 'RETO / PRUEBA', 'icon' => '⚡', 'color_hex' => '#f97316', 'order_index' => 6],
            ['production_type' => 'reality', 'value' => 'ELIMINACION',      'label' => 'ELIMINACIÓN',   'icon' => '🚪', 'color_hex' => '#dc2626', 'order_index' => 7],
            ['production_type' => 'reality', 'value' => 'TRANSICION',       'label' => 'TRANSICIÓN',    'icon' => '⏭️', 'color_hex' => '#94a3b8', 'order_index' => 8],
            ['production_type' => 'reality', 'value' => 'CIERRE',           'label' => 'CIERRE',        'icon' => '🏁', 'color_hex' => '#3b82f6', 'order_index' => 9],
        ];

        foreach ($tipos as $tipo) {
            DB::table('segment_types')->insert(array_merge($tipo, [
                'active'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('segment_types');
    }
};