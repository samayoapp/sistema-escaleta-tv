<?php
namespace App\Http\Controllers;

use App\Models\SegmentLibrary;
use App\Models\Segment;
use App\Models\Block;
use App\Models\Rundown;
use App\Config\SegmentTypes;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    // Lista para el modal (JSON)
    public function index(Request $request)
    {
        $items = SegmentLibrary::orderBy('title')->get();
        return response()->json($items);
    }

    // Crear desde cero
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type'  => 'required|string',
        ]);

        SegmentLibrary::create([
            'title'            => $request->title,
            'type'             => $request->type,
            'duration_seconds' => $request->duration_seconds ?? 60,
            'has_script'       => $request->boolean('has_script'),
            'script_content'   => $request->script_content,
            'production_notes' => $request->production_notes,
            'created_by'       => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Ítem guardado en biblioteca.');
    }

    // Guardar desde un segmento existente
    public function fromSegment(Request $request, $id)
    {
        $segment = Segment::findOrFail($id);

        SegmentLibrary::create([
            'title'            => $segment->title,
            'type'             => $segment->type,
            'duration_seconds' => $segment->duration_seconds,
            'has_script'       => $segment->has_script,
            'script_content'   => $segment->script_content,
            'production_notes' => $segment->production_notes,
            'created_by'       => auth()->id(),
        ]);

        return response()->json(['ok' => true, 'message' => '✓ Guardado en biblioteca']);
    }

    // Insertar ítem de biblioteca en un bloque
    public function insertIntoBlock(Request $request, $id)
    {
        $item  = SegmentLibrary::findOrFail($id);
        $block = Block::findOrFail($request->block_id);

        $maxOrder = $block->segments()->max('order_index') ?? 0;

        $segment = Segment::create([
            'rundown_id'       => $block->rundown_id,
            'block_id'         => $block->id,
            'title'            => $item->title,
            'type'             => $item->type,
            'duration_seconds' => $item->duration_seconds,
            'has_script'       => $item->has_script,
            'script_content'   => $item->script_content,
            'production_notes' => $item->production_notes,
            'order_index'      => $maxOrder + 1,
        ]);

        // Devolver tabla actualizada via HTMX
        $rundown = Rundown::with([
            'show',
            'blocks'          => fn($q) => $q->orderBy('order_index'),
            'blocks.segments' => fn($q) => $q->orderBy('order_index'),
        ])->findOrFail($block->rundown_id);

        $locked = app(\App\Http\Controllers\RundownController::class)
            ->calcLocked($rundown);

        return response(view('partials.table-body', compact('rundown', 'locked'))->render())
            ->withHeaders(['HX-Trigger' => json_encode(['refreshTime' => true])]);
    }

    public function destroy($id)
    {
        SegmentLibrary::findOrFail($id)->delete();
        return response()->json(['ok' => true]);
    }
}