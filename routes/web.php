<?php

use App\Http\Controllers\RundownController;
use App\Http\Controllers\ShowController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::get('/segment/{id}/edit',               [RundownController::class, 'editSegment']);
Route::post('/segment/{id}/update-script',     [RundownController::class, 'updateScript']);
Route::post('/segment/{id}/update-field',      [RundownController::class, 'updateField']);
Route::delete('/segment/{id}',                 [RundownController::class, 'deleteSegment']);
Route::post('/block/{blockId}/add-segment',    [RundownController::class, 'addSegment']);
Route::post('/rundown/{id}/add-block',         [RundownController::class, 'addBlock']);
Route::post('/block/{id}/update',              [RundownController::class, 'updateBlock']);
Route::delete('/block/{id}',                   [RundownController::class, 'deleteBlock']);
Route::post('/rundown/{id}/reorder',           [RundownController::class, 'reorder']);
Route::get('/rundown/{id}/get-time',           [RundownController::class, 'getTime']);
Route::get('/rundown/{id}/prompter',           [RundownController::class, 'prompter']);
Route::get('/rundown/{id}/pdf',                [RundownController::class, 'generatePdf']);
Route::post('/rundown/{id}/update-time',       [RundownController::class, 'updateTime']);
Route::get('/rundown/{id}/pdf-escaleta', [RundownController::class, 'generatePdfEscaleta']);

// ── Shows ──────────────────────────────────────────────
Route::get('/',                                    [ShowController::class, 'index']);
Route::post('/shows',                              [ShowController::class, 'store']);
Route::post('/shows/{id}/update',                  [ShowController::class, 'update']);
Route::get('/shows/{id}',                          [ShowController::class, 'show']);
Route::post('/shows/{id}/rundowns',                [ShowController::class, 'createRundown']);
Route::post('/rundown/{id}/duplicate',             [ShowController::class, 'duplicateRundown']);
Route::post('/shows/{id}/archive',                 [ShowController::class, 'archive']);
Route::delete('/rundown/{id}/delete',              [ShowController::class, 'deleteRundown']);

// ── Rundown ────────────────────────────────────────────
Route::get('/rundown/{id}',                        [RundownController::class, 'index']);