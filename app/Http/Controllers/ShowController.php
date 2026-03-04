<?php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\Rundown;
use App\Models\Block;
use App\Models\Segment;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    // Lista de todos los shows
    public function index()
    {
        $shows = Show::withCount('rundowns')
            ->orderBy('status')
            ->orderBy('title')
            ->get();

        return view('shows.index', compact('shows'));
    }

    // Crear show
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
        ]);

        Show::create($request->only(['title', 'description', 'channel']));

        return redirect('/');
    }

    // Editar show
    public function update(Request $request, $id)
    {
        $show = Show::findOrFail($id);
        $show->update($request->only(['title', 'description', 'channel', 'status']));
        return redirect('/');
    }

    // Ver escaletas de un show
    public function show($id)
    {
        $show = Show::with(['rundowns'])->findOrFail($id);
        return view('shows.rundowns', compact('show'));
    }

    // Crear nueva escaleta para un show
    public function createRundown(Request $request, $id)
    {
        $request->validate([
            'air_date' => 'required|date',
            'air_time' => 'required',
        ]);

        $rundown = Rundown::create([
            'show_id'  => $id,
            'air_date' => $request->air_date,
            'air_time' => $request->air_time,
            'status'   => 'borrador',
        ]);

        return redirect('/rundown/' . $rundown->id);
    }

    // Duplicar escaleta existente
    public function duplicateRundown(Request $request, $id)
    {
        $request->validate([
            'air_date' => 'required|date',
            'air_time' => 'required',
        ]);

        $original = Rundown::with([
            'blocks'          => fn($q) => $q->orderBy('order_index'),
            'blocks.segments' => fn($q) => $q->orderBy('order_index'),
        ])->findOrFail($id);

        // Crear nuevo rundown
        $nuevo = Rundown::create([
            'show_id'  => $original->show_id,
            'air_date' => $request->air_date,
            'air_time' => $request->air_time,
            'status'   => 'borrador',
        ]);

        // Copiar bloques y segmentos
        foreach ($original->blocks as $block) {
            $nuevoBlock = Block::create([
                'rundown_id'  => $nuevo->id,
                'title'       => $block->title,
                'order_index' => $block->order_index,
            ]);

            foreach ($block->segments as $segment) {
                Segment::create([
                    'rundown_id'       => $nuevo->id,
                    'block_id'         => $nuevoBlock->id,
                    'title'            => $segment->title,
                    'type'             => $segment->type,
                    'duration_seconds' => $segment->duration_seconds,
                    'order_index'      => $segment->order_index,
                    // No copiamos script_content — cada emisión tiene su propio guion
                ]);
            }
        }

        return redirect('/rundown/' . $nuevo->id);
    }

    // Archivar / activar show
    public function archive($id)
    {
        $show = Show::findOrFail($id);
        $show->status = $show->status === 'active' ? 'archived' : 'active';
        $show->save();
        return redirect('/');
    }

    // Eliminar escaleta
    public function deleteRundown($id)
    {
        $rundown = Rundown::findOrFail($id);
        $showId  = $rundown->show_id;
        $rundown->delete();
        return redirect('/shows/' . $showId);
    }
}