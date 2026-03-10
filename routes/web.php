<?php

use App\Http\Controllers\ShowController;
use App\Http\Controllers\RundownController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ── AUTH (generado por Breeze) ────────────────────────────────────────────────
require __DIR__.'/auth.php';

// ── RUTAS PROTEGIDAS ─────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // ── Shows (solo Admin puede crear/editar/archivar/eliminar shows) ─────────
    Route::get('/',                     [ShowController::class, 'index']);
    Route::post('/shows',               [ShowController::class, 'store'])
        ->middleware('role:admin');
    Route::post('/shows/{id}/update',   [ShowController::class, 'update'])
        ->middleware('role:admin');
    Route::post('/shows/{id}/archive',  [ShowController::class, 'archive'])
        ->middleware('role:admin');
    Route::delete('/shows/{id}',        [ShowController::class, 'destroy'])
        ->middleware('role:admin');
    Route::get('/shows/{id}',           [ShowController::class, 'show']);

    // ── Escaletas ─────────────────────────────────────────────────────────────
    Route::post('/shows/{id}/rundowns',             [ShowController::class, 'createRundown'])
        ->middleware('role:admin,editor');
    Route::post('/rundown/{id}/duplicate',          [ShowController::class, 'duplicateRundown'])
        ->middleware('role:admin,editor');
    Route::post('/rundown/{id}/update-datetime',    [ShowController::class, 'updateRundownDatetime'])
        ->middleware('role:admin,editor');
    Route::post('/rundown/{id}/aprobar',            [ShowController::class, 'aprobarRundown'])
        ->middleware('role:admin');
    Route::post('/rundown/{id}/desaprobar',         [ShowController::class, 'desaprobarRundown'])
        ->middleware('role:admin');
    Route::delete('/rundown/{id}/delete',           [ShowController::class, 'deleteRundown'])
        ->middleware('role:admin');

    // ── Editor de Rundown ─────────────────────────────────────────────────────
    Route::get('/rundown/{id}',                     [RundownController::class, 'index']);
    Route::post('/rundown/{id}/add-block',          [RundownController::class, 'addBlock'])
        ->middleware('role:admin,editor');
    Route::post('/rundown/{id}/reorder',            [RundownController::class, 'reorder'])
        ->middleware('role:admin,editor');
    Route::post('/rundown/{id}/update-time',        [RundownController::class, 'updateTime'])
        ->middleware('role:admin,editor');
    Route::get('/rundown/{id}/get-time',            [RundownController::class, 'getTime']);
    Route::get('/rundown/{id}/prompter',            [RundownController::class, 'prompter']);
    Route::get('/rundown/{id}/pdf',                 [RundownController::class, 'generatePdf']);
    Route::get('/rundown/{id}/pdf-escaleta',        [RundownController::class, 'generatePdfEscaleta']);

    // ── Bloques ───────────────────────────────────────────────────────────────
    Route::post('/block/{id}/update',               [RundownController::class, 'updateBlock'])
        ->middleware('role:admin,editor');
    Route::delete('/block/{id}',                    [RundownController::class, 'deleteBlock'])
        ->middleware('role:admin,editor');
    Route::post('/block/{id}/add-segment',          [RundownController::class, 'addSegment'])
        ->middleware('role:admin,editor');

    // ── Segmentos ─────────────────────────────────────────────────────────────
    Route::get('/segment/{id}/edit',                [RundownController::class, 'editSegment']);
    Route::post('/segment/{id}/update-field',       [RundownController::class, 'updateField'])
        ->middleware('role:admin,editor');
    Route::post('/segment/{id}/update-script',      [RundownController::class, 'updateScript'])
        ->middleware('role:admin,editor');
    Route::post('/segment/{id}/toggle-script',      [RundownController::class, 'toggleScript'])
        ->middleware('role:admin,editor');
    Route::post('/segment/{id}/toggle-prompter',    [RundownController::class, 'togglePrompter'])
        ->middleware('role:admin,editor');
    Route::post('/segment/insert-after/{segmentId}',[RundownController::class, 'insertSegmentAfter'])
        ->middleware('role:admin,editor');
    Route::delete('/segment/{id}',                  [RundownController::class, 'deleteSegment'])
        ->middleware('role:admin,editor');

    // ── Admin: Usuarios (solo Admin) ──────────────────────────────────────────
    Route::get('/admin/usuarios',           [UserController::class, 'index'])
        ->middleware('role:admin');
    Route::post('/admin/usuarios',          [UserController::class, 'store'])
        ->middleware('role:admin');
    Route::put('/admin/usuarios/{id}',      [UserController::class, 'update'])
        ->middleware('role:admin');
    Route::delete('/admin/usuarios/{id}',   [UserController::class, 'destroy'])
        ->middleware('role:admin');


    Route::post('/segment/{id}/update-notes', [RundownController::class, 'updateNotes'])
        ->middleware('role:admin,editor');
});
