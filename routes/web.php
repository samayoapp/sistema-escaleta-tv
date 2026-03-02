<?php

use App\Http\Controllers\RundownController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Segment;
use App\Models\Rundown;

// 1. Página de inicio (Escaleta)
Route::get('/', [RundownController::class, 'index']);

// 2. Cargar el editor de guion lateral
Route::get('/segment/{id}/edit', function($id) {
    $segment = Segment::findOrFail($id);
    return view('editor-segmento', compact('segment'));
});

// 3. Guardar cambios en el guion (Autosave del textarea)
Route::post('/segment/{id}/update', function(Request $request, $id) {
    $segment = Segment::findOrFail($id);
    $segment->update(['script_content' => $request->script_content]);
    return '<span class="text-green-400 font-bold">✓ ¡GUARDADO EN DB! (' . now()->format('H:i:s') . ')</span>';
});

// 4. Actualizar Título o Duración desde la tabla
Route::post('/segment/{id}/update-field', function(Request $request, $id) {
    $segment = \App\Models\Segment::findOrFail($id);
    $segment->update($request->only(['title', 'duration_seconds']));

    return response("")->header('HX-Trigger', 'refreshTime');
});

// 5. RUTA PARA BORRAR (Esta es la que faltaba)
Route::delete('/segment/{id}', function($id) {
    $segment = \App\Models\Segment::findOrFail($id);
    $rundownId = $segment->rundown_id;
    $segment->delete();

    // 1. Re-numerar los segmentos que quedan en la base de datos
    $segments = \App\Models\Segment::where('rundown_id', $rundownId)
                ->orderBy('order_index')
                ->get();
    
    foreach ($segments as $index => $s) {
        $s->update(['order_index' => $index + 1]);
    }

    // 2. Traer el rundown actualizado para la vista
    $rundown = \App\Models\Rundown::with('segments')->findOrFail($rundownId);

    // 3. DEVOLVEMOS SOLO LA TABLA y disparamos los dos refrescos (Tabla y Tiempo)
    return response(view('partials.table-body', compact('rundown'))->render())
        ->withHeaders([
            'HX-Trigger' => json_encode(['refreshTable' => true, 'refreshTime' => true])
        ]);
});

// 6. Agregar un nuevo segmento
Route::post('/rundown/{id}/add-segment', function($id) {
    $rundown = \App\Models\Rundown::findOrFail($id);
    
    $segment = \App\Models\Segment::create([
        'rundown_id' => $id,
        'order_index' => $rundown->segments()->count() + 1,
        'title' => 'NUEVO BLOQUE',
        'duration_seconds' => 60,
    ]);

    // 1. Solo enviamos el HTML de la fila
    $filaHtml = view('partials.segment-row', compact('segment'))->render();

    // 2. En lugar de concatenar, disparamos un evento de HTMX
    return response($filaHtml)
        ->withHeaders([
            'HX-Trigger' => 'refreshTime'
        ]);
});

Route::get('/rundown/{id}/get-time', function($id) {
    $rundown = \App\Models\Rundown::findOrFail($id);
    return view('partials.total-time', compact('rundown'));
});

// 7. Ver Teleprompter
Route::get('/rundown/{id}/prompter', function($id) {
    $rundown = Rundown::with(['show', 'segments' => function($q) {
        $q->orderBy('order_index', 'asc');
    }])->findOrFail($id);

    return view('teleprompter', compact('rundown'));
});

Route::post('/rundown/{id}/reorder', function(Request $request, $id) {
    $ids = $request->segment_ids;
    
    if($ids) {
        foreach ($ids as $index => $segmentId) {
            \App\Models\Segment::where('id', $segmentId)->update([
                'order_index' => $index + 1
            ]);
        }
    }

    $rundown = \App\Models\Rundown::with(['segments' => function($q) {
        $q->orderBy('order_index', 'asc');
    }])->findOrFail($id);

    // Devolvemos SOLO el cuerpo de la tabla
    return view('partials.table-body', compact('rundown'));
});