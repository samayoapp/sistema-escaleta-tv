<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tipo de producción en shows
        Schema::table('shows', function (Blueprint $table) {
            $table->string('production_type')->default('live')->after('status');
            // 'live'    → Programa en Vivo
            // 'reality' → Reality de TV
            // Próximos: 'documentary', 'talk_show', 'news', etc.
        });

        // Campos de episodio en rundowns (solo aplican en grabado/reality)
        Schema::table('rundowns', function (Blueprint $table) {
            $table->string('episode_name')->nullable()->after('air_time');
            $table->unsignedSmallInteger('episode_number')->nullable()->after('episode_name');
        });
    }

    public function down(): void
    {
        Schema::table('shows', function (Blueprint $table) {
            $table->dropColumn('production_type');
        });
        Schema::table('rundowns', function (Blueprint $table) {
            $table->dropColumn(['episode_name', 'episode_number']);
        });
    }
};
