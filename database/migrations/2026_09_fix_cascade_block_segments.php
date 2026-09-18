<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Limpiar segmentos huérfanos (block_id = null) que quedaron
        //    de eliminaciones anteriores con onDelete('set null')
        DB::table('segments')->whereNull('block_id')->delete();

        // 2. Corregir el cascade en segments.block_id
        //    PostgreSQL requiere drop y recrear la constraint
        Schema::table('segments', function (Blueprint $table) {
            $table->dropForeign(['block_id']);
        });
        Schema::table('segments', function (Blueprint $table) {
            $table->foreignId('block_id')->nullable()->change();
            $table->foreign('block_id')
                  ->references('id')->on('blocks')
                  ->onDelete('cascade'); // ← cascade correcto
        });
    }

    public function down(): void
    {
        Schema::table('segments', function (Blueprint $table) {
            $table->dropForeign(['block_id']);
            $table->foreign('block_id')
                  ->references('id')->on('blocks')
                  ->onDelete('set null');
        });
    }
};
