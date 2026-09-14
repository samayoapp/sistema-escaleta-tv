<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // PostgreSQL no permite ALTER COLUMN directamente en un enum.
        // La forma correcta es:
        // 1. Agregar columna temporal string
        // 2. Copiar los valores
        // 3. Eliminar columna enum
        // 4. Renombrar la temporal

        Schema::table('segments', function (Blueprint $table) {
            $table->string('type_new')->default('PRESENTACION')->after('type');
        });

        // Copiar todos los valores actuales
        DB::statement('UPDATE segments SET type_new = type');

        Schema::table('segments', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('segments', function (Blueprint $table) {
            $table->renameColumn('type_new', 'type');
        });
    }

    public function down(): void
    {
        // Revertir a enum solo si todos los valores son válidos para live
        // En práctica no hace falta revertir — dejamos como string
    }
};
