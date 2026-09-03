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

        // Calcular locked con timezone Tegucigalpa
        $locked = $this->calcLocked($rundown);

        return response(view('partials.table-body', compact('rundown', 'locked'))->render())
            ->withHeaders(['HX-Trigger' => json_encode(['refreshTime' => true])]);
    }

    // ─── Helper: calcular locked con timezone Tegucigalpa ────────────────────
    private function calcLocked($rundown): bool
    {
        $tz = 'America/Tegucigalpa';
        $airDateTime = \Carbon\Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $rundown->air_date . ' ' . ($rundown->air_time ?? '00:00:00'),
            $tz
        );
        return \Carbon\Carbon::now($tz)->greaterThan($airDateTime->copy()->addHour());
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
    $blockLetra = chr(65 + $blockIndex); // A, B, C...
    $segNum     = $blockLetra . '.' . ($segIndex + 1);

    $locked = $this->calcLocked($rundown);

    return view('editor-segmento', compact('segment', 'segNum', 'locked'));
}

    public function updateScript(Request $request, $id)
    {
        $segment = Segment::findOrFail($id);
        $segment->update(['script_content' => $request->script_content]);
        return '<span class="text-green-400 font-bold">✓ Guardado (' . now()->format('H:i:s') . ')</span>';
    }

    public function updateNotes(Request $request, $id)
    {
        $segment = Segment::findOrFail($id);
        $segment->update(['production_notes' => $request->production_notes]);
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

    public function reorderBlocks(Request $request, $rundownId)
    {
        $blockIds = $request->input('block_ids', []);

        foreach ($blockIds as $index => $blockId) {
            Block::where('id', $blockId)
                ->where('rundown_id', $rundownId) // seguridad: solo bloques de este rundown
                ->update(['order_index' => $index + 1]);
        }

        return $this->renderTable($rundownId);
    }

    // ─── Bloques ──────────────────────────────────────────────────────────────

    public function addBlock($rundownId)
    {
        $rundown = Rundown::findOrFail($rundownId);

        Block::create([
            'rundown_id'  => $rundownId,
            'title'       => '',
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

        return $pdf->stream($filename);
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

        return $pdf->stream($filename);
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

    // Insertar ítem después de un segmento específico
    public function insertSegmentAfter(Request $request, $segmentId)
    {
        if ($segmentId == 0) {
            $block = Block::findOrFail($request->block_id);
            $block->segments()->increment('order_index');
            $newSegment = Segment::create([
                'rundown_id'       => $block->rundown_id,
                'block_id'         => $block->id,
                'order_index'      => 1,
                'title'            => 'NUEVO ÍTEM',
                'type'             => 'PRESENTACION',
                'duration_seconds' => 60,
            ]);
        } else {
            $after = Segment::findOrFail($segmentId);
            $block = Block::findOrFail($after->block_id);
            $block->segments()
                ->where('order_index', '>', $after->order_index)
                ->increment('order_index');
            $newSegment = Segment::create([
                'rundown_id'       => $after->rundown_id,
                'block_id'         => $after->block_id,
                'order_index'      => $after->order_index + 1,
                'title'            => 'NUEVO ÍTEM',
                'type'             => 'PRESENTACION',
                'duration_seconds' => 60,
            ]);
        }

        $rundown = Rundown::with([
            'blocks'          => fn($q) => $q->orderBy('order_index'),
            'blocks.segments' => fn($q) => $q->orderBy('order_index'),
        ])->findOrFail($block->rundown_id);

        $locked = $this->calcLocked($rundown);

        return response(view('partials.table-body', compact('rundown', 'locked'))->render())
            ->withHeaders(['HX-Trigger' => json_encode([
                'refreshTime' => true,
                'focusSegment' => $newSegment->id  // ← ID exacto del nuevo ítem
            ])]);
    }
    // ─── Export / Import ──────────────────────────────────────────────────────

    /**
     * Convierte URLs en texto plano a <a href> clicables para DomPDF.
     */
    public static function linkify(?string $text): string
    {
        if (!$text) return '';
        $escaped = e($text);
        $pattern = '~(https?://[^\s<>"\']+)~i';
        return preg_replace(
            $pattern,
            '<a href="$1" style="color:#2563eb;text-decoration:underline;">$1</a>',
            $escaped
        );
    }

    public function exportRundown($id)
    {
        $rundown = Rundown::with([
            'show',
            'blocks'          => fn($q) => $q->orderBy('order_index'),
            'blocks.segments' => fn($q) => $q->orderBy('order_index'),
        ])->findOrFail($id);

        $payload = [
            'ronup_version' => '1.0',
            'exported_at'   => now()->toIso8601String(),
            'show' => [
                'title'           => $rundown->show->title,
                'channel'         => $rundown->show->channel,
                'description'     => $rundown->show->description,
                'production_type' => $rundown->show->production_type ?? 'live',
            ],
            'rundown' => [
                'air_date'       => $rundown->air_date,
                'air_time'       => $rundown->air_time,
                'status'         => 'borrador',
                'episode_name'   => $rundown->episode_name,
                'episode_number' => $rundown->episode_number,
                'blocks'         => $rundown->blocks->map(fn($block) => [
                    'title'       => $block->title,
                    'order_index' => $block->order_index,
                    'segments'    => $block->segments->map(fn($seg) => [
                        'title'            => $seg->title,
                        'type'             => $seg->type,
                        'duration_seconds' => $seg->duration_seconds,
                        'order_index'      => $seg->order_index,
                        'has_script'       => $seg->has_script,
                        'in_prompter'      => $seg->in_prompter,
                        'script_content'   => $seg->script_content,
                        'production_notes' => $seg->production_notes,
                    ])->values(),
                ])->values(),
            ],
        ];

        $showSlug    = str($rundown->show->title)->slug();
        $date        = $rundown->air_date;
        $episodePart = $rundown->episode_number ? '-ep' . $rundown->episode_number : '';
        $filename    = "ronup-{$showSlug}{$episodePart}-{$date}.json";

        return response()->json($payload, 200, [
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Content-Type'        => 'application/json',
        ]);
    }

    public function importRundown(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:json|max:2048']);

        $contents = file_get_contents($request->file('file')->getRealPath());
        $data     = json_decode($contents, true);

        if (!isset($data['ronup_version'], $data['show'], $data['rundown'])) {
            return back()->withErrors(['file' => 'El archivo no es una escaleta RONUP válida.']);
        }

        $showData    = $data['show'];
        $rundownData = $data['rundown'];

        $show = \App\Models\Show::firstOrCreate(
            ['title' => $showData['title']],
            [
                'channel'         => $showData['channel'] ?? null,
                'description'     => $showData['description'] ?? null,
                'production_type' => $showData['production_type'] ?? 'live',
                'status'          => 'active',
            ]
        );

        $rundown = Rundown::create([
            'show_id'        => $show->id,
            'air_date'       => $rundownData['air_date'],
            'air_time'       => $rundownData['air_time'] ?? '00:00:00',
            'status'         => 'borrador',
            'episode_name'   => $rundownData['episode_name'] ?? null,
            'episode_number' => $rundownData['episode_number'] ?? null,
        ]);

        foreach ($rundownData['blocks'] as $blockData) {
            $block = \App\Models\Block::create([
                'rundown_id'  => $rundown->id,
                'title'       => $blockData['title'] ?? '',
                'order_index' => $blockData['order_index'],
            ]);

            foreach ($blockData['segments'] as $segData) {
                \App\Models\Segment::create([
                    'rundown_id'       => $rundown->id,
                    'block_id'         => $block->id,
                    'title'            => $segData['title'],
                    'type'             => $segData['type'],
                    'duration_seconds' => $segData['duration_seconds'],
                    'order_index'      => $segData['order_index'],
                    'has_script'       => $segData['has_script'] ?? false,
                    'in_prompter'      => $segData['in_prompter'] ?? false,
                    'script_content'   => $segData['script_content'] ?? null,
                    'production_notes' => $segData['production_notes'] ?? null,
                ]);
            }
        }

        return redirect('/rundown/' . $rundown->id)
            ->with('success', 'Escaleta importada correctamente.');
    }

    // Toggle in_prompter
    public function togglePrompter($id)
    {
        $segment = Segment::findOrFail($id);
        $segment->in_prompter = !$segment->in_prompter;
        $segment->save();
        return $this->editSegment($id);
    }


}
