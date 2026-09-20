<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Tabla de tipos de producción ──────────────────────────────────
        Schema::create('production_types', function (Blueprint $table) {
            $table->id();
            $table->string('value')->unique();   // 'live', 'reality', 'documental'
            $table->string('label');             // 'Programa en Vivo'
            $table->string('icon')->default('📺');
            $table->boolean('has_air_time')->default(true);   // ¿tiene hora de inicio?
            $table->boolean('has_lock')->default(true);       // ¿se bloquea automáticamente?
            $table->boolean('has_episode')->default(false);   // ¿tiene nombre/número de episodio?
            $table->integer('order_index')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Sembrar los dos tipos existentes
        DB::table('production_types')->insert([
            [
                'value'        => 'live',
                'label'        => 'Programa en Vivo',
                'icon'         => '📡',
                'has_air_time' => true,
                'has_lock'     => true,
                'has_episode'  => false,
                'order_index'  => 1,
                'active'       => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'value'        => 'reality',
                'label'        => 'Reality de TV',
                'icon'         => '🎬',
                'has_air_time' => false,
                'has_lock'     => false,
                'has_episode'  => true,
                'order_index'  => 2,
                'active'       => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);

        // ── 2. Tabla pivote segment_type ↔ production_type ───────────────────
        Schema::create('production_type_segment_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_type_id')->constrained('production_types')->onDelete('cascade');
            $table->foreignId('segment_type_id')->constrained('segment_types')->onDelete('cascade');
            $table->boolean('active')->default(true);
            $table->integer('order_index')->default(0);
            $table->timestamps();

            $table->unique(['production_type_id', 'segment_type_id']);
        });

        // ── 3. Migrar datos existentes de segment_types a la nueva estructura ─
        // Cada fila en segment_types tiene production_type como string.
        // Convertimos eso en registros en la pivote.
        $liveId    = DB::table('production_types')->where('value', 'live')->value('id');
        $realityId = DB::table('production_types')->where('value', 'reality')->value('id');

        $existingTypes = DB::table('segment_types')->get();

        foreach ($existingTypes as $st) {
            $productionTypeId = match($st->production_type) {
                'live'    => $liveId,
                'reality' => $realityId,
                default   => null,
            };

            if ($productionTypeId) {
                // Verificar que no exista ya (CORTE_COMERCIAL puede estar duplicado)
                $alreadyExists = DB::table('production_type_segment_type')
                    ->where('production_type_id', $productionTypeId)
                    ->where('segment_type_id', $st->id)
                    ->exists();

                if (!$alreadyExists) {
                    DB::table('production_type_segment_type')->insert([
                        'production_type_id' => $productionTypeId,
                        'segment_type_id'    => $st->id,
                        'active'             => $st->active,
                        'order_index'        => $st->order_index,
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                }
            }
        }

        // ── 4. Limpiar columna production_type de segment_types ──────────────
        // Ya no la necesitamos — la relación vive en la pivote.
        // La dejamos por ahora para no romper nada, la quitaremos en Parte 2.
    }

    public function down(): void
    {
        Schema::dropIfExists('production_type_segment_type');
        Schema::dropIfExists('production_types');
    }
};
