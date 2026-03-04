<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Show;
use App\Models\Rundown;
use App\Models\Block;
use App\Models\Segment;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear el programa
        $show = Show::create([
            'title' => 'Noticiero Central',
        ]);

        // 2. Crear el rundown
        $rundown = Rundown::create([
            'show_id'  => $show->id,
            'air_date' => '2026-03-02',
            'air_time' => '15:00:00',
            'status'   => 'produccion',
        ]);

        // 3. Bloques con sus segmentos
        $bloques = [
            [
                'title' => 'BLOQUE 1 - APERTURA',
                'order_index' => 1,
                'segments' => [
                    ['title' => 'PRESENTACIÓN CONDUCTORES',  'type' => 'PRESENTACION',    'duration_seconds' => 60],
                    ['title' => 'TITULARES DEL DÍA',         'type' => 'VIVO',             'duration_seconds' => 90],
                    ['title' => 'NOTA ESPECIAL ECONOMÍA',    'type' => 'VTR',              'duration_seconds' => 120],
                ],
            ],
            [
                'title' => 'BLOQUE 2 - NACIONALES',
                'order_index' => 2,
                'segments' => [
                    ['title' => 'REFORMA EDUCATIVA',         'type' => 'VIVO',             'duration_seconds' => 180],
                    ['title' => 'REPORTE DESDE CONGRESO',    'type' => 'VTR',              'duration_seconds' => 150],
                    ['title' => 'COMENTARIO EDITORIAL',      'type' => 'OFF',              'duration_seconds' => 60],
                ],
            ],
            [
                'title' => 'CORTE COMERCIAL',
                'order_index' => 3,
                'segments' => [
                    ['title' => 'PAUTA COMERCIAL 1',         'type' => 'CORTE_COMERCIAL',  'duration_seconds' => 120],
                ],
            ],
            [
                'title' => 'BLOQUE 3 - CIERRE',
                'order_index' => 4,
                'segments' => [
                    ['title' => 'DEPORTES RESUMEN',          'type' => 'VTR',              'duration_seconds' => 90],
                    ['title' => 'NOTA SECA CULTURA',         'type' => 'NOTA_SECA',        'duration_seconds' => 45],
                    ['title' => 'CIERRE Y DESPEDIDA',        'type' => 'CIERRE',           'duration_seconds' => 30],
                ],
            ],
        ];

        foreach ($bloques as $bloqueData) {
            $block = Block::create([
                'rundown_id'  => $rundown->id,
                'title'       => $bloqueData['title'],
                'order_index' => $bloqueData['order_index'],
            ]);

            foreach ($bloqueData['segments'] as $index => $segData) {
                Segment::create([
                    'rundown_id'       => $rundown->id,
                    'block_id'         => $block->id,
                    'title'            => $segData['title'],
                    'type'             => $segData['type'],
                    'duration_seconds' => $segData['duration_seconds'],
                    'order_index'      => $index + 1,
                ]);
            }
        }
    }
}