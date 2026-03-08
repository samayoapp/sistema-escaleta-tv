<?php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\Rundown;
use App\Models\Block;
use App\Models\Segment;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    public function index()
    {
        $shows = Show::withCount('rundowns')
            ->orderBy('status')
            ->orderBy('title')
            ->get();

        return view('shows.index', compact('shows'));
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255']);
        Show::create($request->only(['title', 'description', 'channel']));
        return redirect('/');
    }

    public function update(Request $request, $id)
    {
        $show = Show::findOrFail($id);
        $show->update($request->only(['title', 'description', 'channel', 'status']));
        return redirect('/shows/' . $id);
    }

    public function show($id)
    {
        $show = Show::with(['rundowns'])->findOrFail($id);
        return view('shows.rundowns', compact('show'));
    }

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

        $nuevo = Rundown::create([
            'show_id'  => $original->show_id,
            'air_date' => $request->air_date,
            'air_time' => $request->air_time,
            'status'   => 'borrador',
        ]);

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
                ]);
            }
        }

        return redirect('/rundown/' . $nuevo->id);
    }

    // Editar fecha y hora de una escaleta
    public function updateRundownDatetime(Request $request, $id)
    {
        $request->validate([
            'air_date' => 'required|date',
            'air_time' => 'required',
        ]);

        $rundown = Rundown::findOrFail($id);
        $rundown->air_date = $request->air_date;
        $rundown->air_time = $request->air_time;
        $rundown->save();

        return redirect('/shows/' . $rundown->show_id);
    }

    // Aprobar escaleta
    public function aprobarRundown($id)
    {
        $rundown = Rundown::findOrFail($id);
        $rundown->status = 'aprobada';
        $rundown->save();
        return redirect('/shows/' . $rundown->show_id);
    }

    // Regresar a borrador
    public function desaprobarRundown($id)
    {
        $rundown = Rundown::findOrFail($id);
        $rundown->status = 'borrador';
        $rundown->save();
        return redirect('/shows/' . $rundown->show_id);
    }

    public function archive($id)
    {
        $show = Show::findOrFail($id);
        $show->status = $show->status === 'active' ? 'archived' : 'active';
        $show->save();
        return redirect('/');
    }

    public function deleteRundown($id)
    {
        $rundown = Rundown::findOrFail($id);
        $showId  = $rundown->show_id;
        $rundown->delete();
        return redirect('/shows/' . $showId);
    }

    public function destroy($id)
    {
        Show::findOrFail($id)->delete();
        return redirect('/');
    }
}
