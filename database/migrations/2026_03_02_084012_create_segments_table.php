<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rundown_id')->constrained()->onDelete('cascade');
            $table->foreignId('block_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('order_index')->default(0);
            $table->string('title')->default('SIN TÍTULO');
            $table->integer('duration_seconds')->default(0);
            $table->longText('script_content')->nullable();
            $table->enum('type', [
                'VIVO',
                'VTR',
                'OFF',
                'CORTE_COMERCIAL',
                'NOTA_SECA',
                'PRESENTACION',
                'CIERRE'
            ])->default('PRESENTACION');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('segments');
    }
};
