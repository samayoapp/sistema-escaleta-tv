<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
        public function run(): void
    {
        // Creamos un programa de ejemplo
        $show = \App\Models\Show::create(['title' => 'Zorin News 6PM']);

        // Creamos una escaleta para hoy
        $rundown = \App\Models\Rundown::create([
            'show_id' => $show->id,
            'air_date' => now(),
        ]);

        // Creamos 3 segmentos de ejemplo
        for ($i = 1; $i <= 3; $i++) {
            \App\Models\Segment::create([
                'rundown_id' => $rundown->id,
                'order_index' => $i,
                'title' => "Noticia Importante #$i",
                'duration_seconds' => 120,
                'script_content' => "Este es el guion de la noticia $i. Aquí va lo que lee el presentador.",
            ]);
        }
    }
}
