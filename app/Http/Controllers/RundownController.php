<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Rundown;
use App\Models\Segment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RundownController extends Controller
{
    // ─── Helper privado ───────────────────────────────────────────────────────
    private function renderTable($rundownId)
    {
        $rundown = Rundown::with([
            'blocks'           => fn($q) => $q->orderBy('order_index'),
            'blocks.segments'  => fn($q) => $q->orderBy('order_index'),
        ])->findOrFail($rundownId);

        return response(view('partials.table-body', compact('rundown'))->render())
            ->withHeaders(['HX-Trigger' => json_encode(['refreshTime' => true])]);
    }

    // ─── Vista principal — recibe ID del rundown ──────────────────────────────
    public function index($id)
    {
        $rundown = Rundown::with([
            'show',
            'blocks'          => fn($q) => $q->orderBy('order_index'),
            'blocks.segments' => fn($q) => $q->orderBy('order_index'),
        ])->findOrFail($id);

        return view('rundown', compact('rundown'));
    }

    // ─── Segmentos ────────────────────────────────────────────────────────────

public function editSegment($id)
{
    $segment = Segment::with(['block'])->findOrFail($id);

    // Calcular el código B1.2 etc.
    $block    = $segment->block;
    $rundown  = Rundown::with([
        'blocks'          => fn($q) => $q->orderBy('order_index'),
        'blocks.segments' => fn($q) => $q->orderBy('order_index'),
    ])->findOrFail($segment->rundown_id);

    $blockIndex = $rundown->blocks->search(fn($b) => $b->id === $block->id);
    $segIndex   = $block->segments->search(fn($s) => $s->id === $segment->id);
    $segNum     = 'B' . ($blockIndex + 1) . '.' . ($segIndex + 1);

    return view('editor-segmento', compact('segment', 'segNum'));
}

    public function updateScript(Request $request, $id)
    {
        $segment = Segment::findOrFail($id);
        $segment->update(['script_content' => $request->script_content]);
        return '<span class="text-green-400 font-bold">✓ Guardado (' . now()->format('H:i:s') . ')</span>';
    }

    public function updateField(Request $request, $id)
    {
        $segment = Segment::findOrFail($id);
        $segment->update($request->only(['title', 'duration_seconds', 'type']));
        return $this->renderTable($segment->rundown_id);
    }

    public function addSegment(Request $request, $blockId)
    {
        $block = Block::findOrFail($blockId);

        Segment::create([
            'rundown_id'       => $block->rundown_id,
            'block_id'         => $block->id,
            'order_index'      => $block->segments()->count() + 1,
            'title'            => 'NUEVO ÍTEM',
            'type'             => 'PRESENTACION',
            'duration_seconds' => 60,
        ]);

        return $this->renderTable($block->rundown_id);
    }

    public function deleteSegment($id)
    {
        $segment = Segment::findOrFail($id);
        $rundownId = $segment->rundown_id;
        $blockId = $segment->block_id;

        $segment->delete();

        if ($blockId) {
            $block = Block::find($blockId);
            if ($block) {
                $block->segments()->orderBy('order_index')
                    ->get()->each(fn($s, $i) => $s->update(['order_index' => $i + 1]));
            }
        }

        return $this->renderTable($rundownId);
    }

    public function reorder(Request $request, $rundownId)
    {
        if ($request->blocks) {
            foreach ($request->blocks as $blockId => $segmentIds) {
                foreach ($segmentIds as $index => $segmentId) {
                    Segment::where('id', $segmentId)->update([
                        'block_id'    => $blockId,
                        'order_index' => $index + 1,
                    ]);
                }
            }
        }

        return $this->renderTable($rundownId);
    }

    // ─── Bloques ──────────────────────────────────────────────────────────────

    public function addBlock($rundownId)
    {
        $rundown = Rundown::findOrFail($rundownId);

        Block::create([
            'rundown_id'  => $rundownId,
            'title'       => 'NUEVO BLOQUE',
            'order_index' => $rundown->blocks()->count() + 1,
        ]);

        return $this->renderTable($rundownId);
    }

    public function updateBlock(Request $request, $id)
    {
        $block = Block::findOrFail($id);
        $block->update($request->only('title'));
        return response()->noContent();
    }

    public function deleteBlock($id)
    {
        $block = Block::findOrFail($id);
        $rundownId = $block->rundown_id;
        $block->delete();

        return $this->renderTable($rundownId);
    }

    // ─── Otros ────────────────────────────────────────────────────────────────

    public function getTime($id)
    {
        $rundown = Rundown::with(['blocks.segments'])->findOrFail($id);
        return view('partials.total-time', compact('rundown'));
    }

    public function prompter($id)
    {
        $rundown = Rundown::with([
            'show',
            'blocks'          => fn($q) => $q->orderBy('order_index'),
            'blocks.segments' => fn($q) => $q->orderBy('order_index'),
        ])->findOrFail($id);

        return view('teleprompter', compact('rundown'));
    }

    public function updateTime(Request $request, $id)
    {
        $rundown = Rundown::findOrFail($id);
        $rundown->air_time = $request->input('air_time');
        $rundown->save();

        return $this->renderTable($id);
    }

    // ─── PDF ──────────────────────────────────────────────────────────────────

    public function generatePdf($id)
    {
        $rundown = Rundown::with([
            'show',
            'blocks'          => fn($q) => $q->orderBy('order_index'),
            'blocks.segments' => fn($q) => $q->orderBy('order_index'),
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.guion', compact('rundown'))
            ->setPaper('letter', 'portrait');

        $filename = 'guion-' . str($rundown->show->title)->slug() . '-' . $rundown->air_date . '.pdf';

        return $pdf->download($filename);
    }

    public function generatePdfEscaleta($id)
    {
        $rundown = Rundown::with([
            'show',
            'blocks'          => fn($q) => $q->orderBy('order_index'),
            'blocks.segments' => fn($q) => $q->orderBy('order_index'),
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.escaleta', compact('rundown'))
            ->setPaper('letter', 'landscape');

        $filename = 'escaleta-' . str($rundown->show->title)->slug() . '-' . $rundown->air_date . '.pdf';

        return $pdf->download($filename);
    }

    public function toggleScript($id)
    {
        $segment = Segment::findOrFail($id);
        $segment->has_script = !$segment->has_script;
        $segment->save();

        // Recarga tabla Y panel de propiedades
        $this->renderTable($segment->rundown_id); // actualiza tabla en background
        return $this->editSegment($id); // devuelve panel actualizado
    }
}
