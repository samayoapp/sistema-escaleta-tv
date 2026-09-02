<?php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\Rundown;
use App\Models\Block;
use App\Models\Segment;
use App\Config\SegmentTypes;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    public function index()
    {
        $shows = Show::withCount('rundowns')
            ->orderBy('status')
            ->orderBy('title')
            ->get();

        $productionTypes = SegmentTypes::productionTypes();

        return view('shows.index', compact('shows', 'productionTypes'));
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255']);
        Show::create($request->only(['title', 'description', 'channel', 'production_type']));
        return redirect('/');
    }

    public function update(Request $request, $id)
    {
        $show = Show::findOrFail($id);
        $show->update($request->only(['title', 'description', 'channel', 'status', 'production_type']));
        return redirect('/shows/' . $id);
    }

    public function show($id)
    {
        $show = Show::with(['rundowns'])->findOrFail($id);
        return view('shows.rundowns', compact('show'));
    }

    public function createRundown(Request $request, $id)
    {
        $show = Show::findOrFail($id);

        // Validación base
        $rules = ['air_date' => 'required|date'];

        // air_time solo es obligatorio en programas en vivo
        if ($show->isLive()) {
            $rules['air_time'] = 'required';
        }

        // Campos de episodio para reality
        if ($show->isReality()) {
            $rules['episode_number'] = 'nullable|integer|min:1';
            $rules['episode_name']   = 'nullable|string|max:255';
        }

        $request->validate($rules);

        $rundown = Rundown::create([
            'show_id'        => $id,
            'air_date'       => $request->air_date,
            'air_time'       => $show->isLive() ? $request->air_time : '00:00:00',
            'status'         => 'borrador',
            'episode_name'   => $request->episode_name,
            'episode_number' => $request->episode_number,
        ]);

        return redirect('/rundown/' . $rundown->id);
    }

    public function duplicateRundown(Request $request, $id)
    {
        $rules = ['air_date' => 'required|date'];

        $original = Rundown::with([
            'show',
            'blocks'          => fn($q) => $q->orderBy('order_index'),
            'blocks.segments' => fn($q) => $q->orderBy('order_index'),
        ])->findOrFail($id);

        if ($original->show->isLive()) {
            $rules['air_time'] = 'required';
        }

        $request->validate($rules);

        $nuevo = Rundown::create([
            'show_id'        => $original->show_id,
            'air_date'       => $request->air_date,
            'air_time'       => $original->show->isLive() ? $request->air_time : '00:00:00',
            'status'         => 'borrador',
            'episode_name'   => $request->episode_name ?? $original->episode_name,
            'episode_number' => $request->episode_number ?? null,
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

    public function updateRundownDatetime(Request $request, $id)
    {
        $rundown = Rundown::with('show')->findOrFail($id);

        $rules = ['air_date' => 'required|date'];
        if ($rundown->show->isLive()) {
            $rules['air_time'] = 'required';
        }

        $request->validate($rules);

        $rundown->air_date       = $request->air_date;
        $rundown->air_time       = $rundown->show->isLive() ? $request->air_time : '00:00:00';
        $rundown->episode_name   = $request->episode_name;
        $rundown->episode_number = $request->episode_number;
        $rundown->save();

        return redirect('/shows/' . $rundown->show_id);
    }

    public function aprobarRundown($id)
    {
        $rundown = Rundown::findOrFail($id);
        $rundown->status = 'aprobada';
        $rundown->save();
        return redirect('/shows/' . $rundown->show_id);
    }

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
