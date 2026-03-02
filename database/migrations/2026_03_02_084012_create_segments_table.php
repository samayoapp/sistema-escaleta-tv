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
            $table->integer('order_index'); // Posición en la tabla
            $table->string('title'); // Título de la noticia o bloque
            $table->integer('duration_seconds')->default(0);
            $table->longText('script_content')->nullable(); // El Guion Literario
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
