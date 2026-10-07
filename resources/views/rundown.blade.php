<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $rundown->show->title }} — @if($rundown->show->hasEpisode() && $rundown->episode_number)EP {{ $rundown->episode_number }}: @endif{{ $rundown->getEditionTitle() }} | RONUP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }

        tr.segment-selected td {
            background-color: rgba(234, 179, 8, 0.06) !important;
        }
        tr.segment-selected td:first-child {
            box-shadow: inset 3px 0 0 #eab308;
        }
        tr.segment-selected {
            outline: 1.5px dashed rgba(234, 179, 8, 0.6);
            outline-offset: -1px;
        }
    </style>
</head>
<body class="bg-gray-900 text-white font-sans">

@include('partials.navbar')

<div class="p-6">
@php
    $locked = $rundown->isLocked();
    $tz = 'America/Tegucigalpa';
    $airTimeStr = ($rundown->air_time && $rundown->air_time !== '00:00:00')
        ? $rundown->air_time
        : ($rundown->show->hasAirTime() ? ($rundown->air_time ?? '00:00:00') : '23:59:59');
    $airDateStr = $rundown->air_date instanceof \Carbon\Carbon
        ? $rundown->air_date->format('Y-m-d')
        : substr((string)$rundown->air_date, 0, 10);
    $airDateTime = \Carbon\Carbon::createFromFormat(
        'Y-m-d H:i:s',
        $airDateStr . ' ' . $airTimeStr,
        $tz
    );
@endphp

<div class="max-w-7xl mx-auto">

    {{-- BANNER VENCIDA --}}
    @if($locked)
    <div class="mb-6 bg-red-950/30 border-2 border-dashed border-red-700/70 rounded-lg px-5 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-2xl">🔴</span>
            <div>
                <div class="text-red-400 font-bold uppercase tracking-widest text-sm">Escaleta Vencida — Solo lectura</div>
                <div class="text-red-500/60 text-xs mt-0.5">
                    Venció el {{ $airDateTime->format('d/m/Y') }} a las {{ $airDateTime->format('H:i') }} (Tegucigalpa).
                    Para reactivar, cambia la fecha/hora desde el repositorio de escaletas.
                </div>
            </div>
        </div>
        <a href="/shows/{{ $rundown->show_id }}"
           class="text-xs text-red-400/70 hover:text-white border border-red-700/50 hover:border-gray-400 px-3 py-2 rounded transition">
            Ir al repositorio →
        </a>
    </div>
    @endif

    {{-- HEADER --}}
    <header class="flex justify-between items-center mb-8 border-b border-gray-700 pb-5 {{ $locked ? 'opacity-60' : '' }}">
        <div>
            <div class="flex items-center gap-3 mb-2 flex-wrap">
                <h1 class="text-3xl font-black tracking-tight {{ $locked ? 'text-gray-500' : 'text-blue-400' }}">
                    {{ $rundown->show->title }}
                </h1>

                @if($rundown->show->hasEpisode())
                    @if($rundown->episode_number || $rundown->episode_name)
                    <span class="text-gray-600 text-2xl font-light">/</span>
                    <div class="flex items-center gap-2 bg-pink-950/40 border border-pink-700/40 px-3 py-1 rounded-lg">
                        @if($rundown->episode_number)
                            <span class="font-mono text-xs font-bold uppercase bg-pink-800/60 text-pink-200 px-2 py-0.5 rounded">
                                EP {{ $rundown->episode_number }}
                            </span>
                        @endif
                        @if($rundown->episode_name)
                            <span class="text-base font-bold text-pink-300">
                                {{ $rundown->episode_name }}
                            </span>
                        @endif
                    </div>
                    @endif
                @else
                    {{-- En vivo: Mostrar badge EN VIVO + Edición/Tema (manual o automático con fecha) --}}
                    <span class="text-gray-600 text-2xl font-light">/</span>
                    <div class="flex items-center gap-2 bg-red-950/40 border border-red-700/40 px-3 py-1 rounded-lg">
                        <span class="flex items-center gap-1.5 font-mono text-xs font-bold uppercase bg-red-800/60 text-red-200 px-2 py-0.5 rounded">
                            <span class="w-2 h-2 rounded-full bg-red-400 animate-pulse"></span>
                            EN VIVO
                        </span>
                        <span class="text-base font-bold text-red-200">
                            {{ $rundown->getEditionTitle() }}
                        </span>
                    </div>
                @endif

                {{-- RELOJ EN TIEMPO REAL GMT-6 --}}
                <div class="flex flex-col items-start ml-2 pl-3 border-l border-gray-800">
                    <div id="reloj-hora"
                         class="font-mono font-bold text-2xl {{ $locked ? 'text-gray-600' : 'text-yellow-400' }} leading-none tabular-nums">
                        --:--:--
                    </div>
                    <div class="text-[9px] text-gray-500 uppercase tracking-widest mt-0.5">
                        Tegucigalpa · GMT-6
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap text-xs">
                <span class="bg-gray-800 border border-gray-700 rounded px-2.5 py-1 text-gray-300 flex items-center gap-1.5">
                    <span class="text-blue-400 font-semibold">📡 Fecha de Aire:</span>
                    <strong class="text-white">{{ \Carbon\Carbon::parse($rundown->air_date)->format('d/m/Y') }}</strong>
                    <span class="text-gray-500 text-[11px]">({{ \Carbon\Carbon::parse($rundown->air_date)->translatedFormat('l') }})</span>
                </span>

                @if($rundown->delivery_date)
                <span class="bg-amber-950/40 border border-amber-700/50 rounded px-2.5 py-1 text-amber-200 flex items-center gap-1.5">
                    <span class="text-amber-400 font-semibold">📅 Fecha de Entrega:</span>
                    <strong class="text-amber-200">{{ \Carbon\Carbon::parse($rundown->delivery_date)->format('d/m/Y') }}</strong>
                    <span class="text-amber-400/70 text-[11px]">({{ \Carbon\Carbon::parse($rundown->delivery_date)->translatedFormat('l') }})</span>
                </span>
                @endif

                @if($rundown->show->hasAirTime())
                <span class="bg-gray-800 border border-gray-700 rounded px-2.5 py-1 text-gray-300 flex items-center gap-1.5">
                    <span class="text-yellow-400 font-semibold">⏰ Hora Inicio:</span>
                    <strong class="font-mono text-yellow-300">{{ substr($rundown->air_time ?? '00:00:00', 0, 5) }}</strong>
                </span>
                @endif
            </div>
        </div>
        <div class="flex gap-2 items-center flex-wrap">
            <a href="/shows/{{ $rundown->show_id }}" class="text-gray-500 hover:text-white transition mr-2">← Volver</a>
            <a href="/rundown/{{ $rundown->id }}/pdf" target="_blank"
                class="bg-gray-600 hover:bg-gray-500 px-4 py-2 rounded text-sm font-bold uppercase transition">
                📄 Guion PDF
            </a>
            <a href="/rundown/{{ $rundown->id }}/pdf-escaleta" target="_blank"
                class="bg-purple-700 hover:bg-purple-600 px-4 py-2 rounded text-sm font-bold uppercase transition">
                📋 Escaleta PDF
            </a>
            <a href="/rundown/{{ $rundown->id }}/prompter" target="_blank"
               class="bg-yellow-600 hover:bg-yellow-500 px-4 py-2 rounded text-sm font-bold uppercase transition">
                📺 Teleprompter
            </a>
            @if(!$locked)
                <div class="bg-green-700 px-4 py-2 rounded text-sm font-bold uppercase">🔴 En Producción</div>
            @else
                <div class="bg-gray-700 px-4 py-2 rounded text-sm font-bold uppercase text-gray-400">📼 Corrida</div>
            @endif
        </div>
    </header>

    {{-- LAYOUT PRINCIPAL --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- COLUMNA IZQUIERDA --}}
        <div class="lg:col-span-2 flex flex-col gap-4">

            <div id="total-duration"
                 class="bg-gray-800 p-4 rounded-lg border border-gray-700 text-right"
                 hx-get="/rundown/{{ $rundown->id }}/get-time"
                 hx-trigger="refreshTime from:body">
                @include('partials.total-time', ['rundown' => $rundown])
            </div>

            <div class="bg-gray-800 rounded-lg shadow-2xl overflow-hidden border {{ $locked ? 'border-gray-700/50' : 'border-gray-700' }}">
                <div class="p-4 bg-gray-700/50 flex justify-between items-center border-b border-gray-700">
                    <h2 class="text-xs font-bold uppercase {{ $locked ? 'text-gray-600' : 'text-gray-400' }} tracking-widest">
                        Estructura del Programa
                    </h2>
                    @if(!$locked)
                        @if(auth()->user()->isEditor())
                        <button
                            onclick="justAddedItem = true"
                            hx-post="/rundown/{{ $rundown->id }}/add-block"
                            hx-target="#tabla-segmentos"
                            hx-swap="innerHTML"
                            class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold py-2 px-4 rounded transition flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            NUEVO BLOQUE
                        </button>
                        @else
                        <button onclick="sinPermiso('Solo editores y admins pueden agregar bloques.')"
                            class="bg-gray-700 text-gray-500 text-xs font-bold py-2 px-4 rounded flex items-center gap-2 cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            NUEVO BLOQUE
                        </button>
                        <button onclick="abrirBiblioteca()"
                        class="bg-gray-700 hover:bg-gray-600 px-3 py-2 rounded text-xs font-bold uppercase tracking-widest transition text-blue-400">
                        📚 Biblioteca
                        </button>
                        @endif
                    @endif
                </div>

                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-700/30 text-gray-400 uppercase text-xs border-b border-gray-700">
                            <th class="px-4 py-3 w-10"></th>
                            <th class="px-4 py-3 w-12">#</th>
                            <th class="px-4 py-3">Título / Tipo</th>
                            <th class="px-4 py-3 w-24 text-center">Duración</th>
                            <th class="px-4 py-3 w-28 text-center text-yellow-500">⏱ Al Aire</th>
                            <th class="px-4 py-3 w-10"></th>
                        </tr>
                    </thead>
                    <tbody id="tabla-segmentos" class="divide-y divide-gray-700/30">
                        @include('partials.table-body', ['rundown' => $rundown, 'locked' => $locked])
                    </tbody>
                </table>
            </div>
        </div>

        {{-- COLUMNA DERECHA: Panel --}}
        <div id="editor-container"
             class="bg-gray-800 rounded-lg p-5 shadow-2xl border border-gray-700 self-start sticky top-6 transition-all">
            <div class="flex flex-col items-center justify-center h-64 text-gray-600 italic text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-3 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/>
                </svg>
                <p class="text-sm">{{ $locked ? 'Solo lectura — Haz clic en un ítem para ver sus propiedades.' : 'Haz clic en un ítem para ver sus propiedades.' }}</p>
            </div>
        </div>

    </div>{{-- fin grid --}}
</div>{{-- fin max-w --}}
</div>{{-- fin p-6 --}}

{{-- FOOTER --}}
<footer class="max-w-7xl mx-auto px-6 mt-24 pb-12 border-t border-gray-800">
    <div class="flex flex-col items-center justify-center gap-2 pt-8">
        <div class="flex items-center gap-2 text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 10l4.553-2.069A1 1 0 0121 8.882v6.236a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
            </svg>
            <span class="text-xs font-bold uppercase tracking-widest text-gray-600">
                {{ $rundown->show->title }}
            </span>
        </div>
        <p class="text-[11px] text-gray-700 tracking-wider">
            &copy; {{ date('Y') }} {{ $rundown->show->channel ? $rundown->show->channel . ' · ' : '' }}Sistema de Producción Televisiva
        </p>
        <p class="text-[10px] text-gray-800 mt-1">
            Escaleta del {{ \Carbon\Carbon::parse($rundown->air_date)->translatedFormat('d \d\e F \d\e Y') }}
            &nbsp;·&nbsp; Generado con ProducciónTV
        </p>
    </div>
</footer>

<script>
    const LOCKED = {{ $locked ? 'true' : 'false' }};
    const CSRF   = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // ── CSRF para HTMX ────────────────────────────────────────────────────
    document.body.addEventListener('htmx:configRequest', (e) => {
        e.detail.headers['X-CSRF-Token'] = CSRF;
    });

    // ── Helper central: actualizar tabla ─────────────────────────────────
    function reloadTabla(html, focusSegmentId) {
        const tbody = document.getElementById('tabla-segmentos');

        // Guardar posición de scroll antes de modificar el DOM
        const savedScroll = window.scrollY;

        tbody.innerHTML = html;

        // CRÍTICO: registrar todos los atributos hx-* de los nuevos elementos
        htmx.process(tbody);

        sortableInstance = null;
        initSortable();
        htmx.trigger(document.body, 'refreshTime');

        // Restaurar scroll inmediatamente — evita el "brinco"
        window.scrollTo({ top: savedScroll, behavior: 'instant' });

        if (selectedSegmentId) {
            const row = document.getElementById('segment-' + selectedSegmentId);
            if (row) row.classList.add('segment-selected');
        }

        if (focusSegmentId) {
            setTimeout(() => {
                const row = document.getElementById('segment-' + focusSegmentId);
                if (row) {
                    const input = row.querySelector('input.seg-title-input');
                    if (input) {
                        input.focus();
                        input.select();
                        input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            }, 60);
        }
    }

    // ── Insertar ítem entre filas (fetch manual, sin HTMX) ───────────────
    function insertarItemDespues(segmentId, blockId) {
        const url = (segmentId == 0)
            ? `/segment/insert-after/0?block_id=${blockId}`
            : `/segment/insert-after/${segmentId}`;

        fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF }
        })
        .then(r => {
            let focusId = null;
            try {
                const trigger = r.headers.get('HX-Trigger');
                if (trigger) focusId = JSON.parse(trigger).focusSegment;
            } catch(e) {}
            return r.text().then(html => ({ html, focusId }));
        })
        .then(({ html, focusId }) => reloadTabla(html, focusId));
    }

    // ── Banderas para Nuevo Bloque y + Ítem ──────────────────────────────
    let justAddedItem  = false;
    let addedToBlockId = null;

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('button[hx-post*="add-segment"]');
        if (btn) {
            justAddedItem = true;
            const match = btn.getAttribute('hx-post').match(/\/block\/(\d+)\/add-segment/);
            addedToBlockId = match ? match[1] : null;
        }
    });

    // ── BEFORE SWAP — guardar scroll antes de que HTMX modifique el DOM ──
    let _savedScroll = 0;
    document.addEventListener('htmx:beforeSwap', function(e) {
        if (e.detail.target.id !== 'tabla-segmentos') return;
        _savedScroll = window.scrollY;
    });

    // ── AFTER SWAP — solo para operaciones HTMX normales ─────────────────
    document.addEventListener('htmx:afterSwap', function(e) {
        if (e.detail.target.id !== 'tabla-segmentos') return;

        // CRÍTICO: registrar atributos hx-* de los nuevos elementos
        htmx.process(e.detail.target);

        // Restaurar scroll — evita el "brinco"
        window.scrollTo({ top: _savedScroll, behavior: 'instant' });

        sortableInstance = null;
        initSortable();

        if (selectedSegmentId) {
            const row = document.getElementById('segment-' + selectedSegmentId);
            if (row) row.classList.add('segment-selected');
        }

        if (!justAddedItem) return;
        justAddedItem = false;

        setTimeout(() => {
            if (!addedToBlockId) {
                // Nuevo bloque → título del bloque
                const blockInputs = document.querySelectorAll('#tabla-segmentos .block-header input[name="title"]');
                if (blockInputs.length > 0) {
                    const last = blockInputs[blockInputs.length - 1];
                    last.focus();
                    last.select();
                    last.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }
            // + Ítem → último ítem del bloque
            const segRows = document.querySelectorAll(`#tabla-segmentos tr.segment-of-${addedToBlockId}`);
            if (segRows.length > 0) {
                const lastInput = segRows[segRows.length - 1].querySelector('input.seg-title-input');
                if (lastInput) {
                    lastInput.focus();
                    lastInput.select();
                    lastInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
            addedToBlockId = null;
        }, 80);
    });

    // ── RELOJ GMT-6 TEGUCIGALPA ───────────────────────────────────────────
    function actualizarReloj() {
        const ahora = new Date();
        // Offset GMT-6 en minutos: -360
        const utc = ahora.getTime() + ahora.getTimezoneOffset() * 60000;
        const gmt6 = new Date(utc + (-6 * 3600000));
        const hh = String(gmt6.getHours()).padStart(2, '0');
        const mm = String(gmt6.getMinutes()).padStart(2, '0');
        const ss = String(gmt6.getSeconds()).padStart(2, '0');
        const el = document.getElementById('reloj-hora');
        if (el) el.textContent = `${hh}:${mm}:${ss}`;
    }
    actualizarReloj();
    setInterval(actualizarReloj, 1000);

    // ── SELECCIÓN ─────────────────────────────────────────────────────────
    let selectedSegmentId = null;

    function seleccionarSegmento(segmentId, row) {
        // Siempre permite seleccionar para ver propiedades, incluso en modo locked
        if (selectedSegmentId === segmentId) {
            deseleccionarSegmento();
            return;
        }

        document.querySelectorAll('tr.segment-selected')
            .forEach(r => r.classList.remove('segment-selected'));

        selectedSegmentId = segmentId;
        row.classList.add('segment-selected');

        const panel = document.getElementById('editor-container');
        panel.classList.add('border-yellow-500/40');
        panel.classList.remove('border-gray-700');

        htmx.ajax('GET', '/segment/' + segmentId + '/edit', {
            target: '#editor-container',
            swap: 'innerHTML'
        });
    }

    function deseleccionarSegmento() {
        selectedSegmentId = null;
        document.querySelectorAll('tr.segment-selected')
            .forEach(r => r.classList.remove('segment-selected'));

        const panel = document.getElementById('editor-container');
        panel.classList.remove('border-yellow-500/40');
        panel.classList.add('border-gray-700');
        panel.innerHTML = `
            <div class="flex flex-col items-center justify-center h-64 text-gray-600 italic text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-3 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/>
                </svg>
                <p class="text-sm">Haz clic en un ítem<br>para ver sus propiedades.</p>
            </div>
        `;
    }

    // ── COLLAPSE / EXPAND ─────────────────────────────────────────────────
    function toggleBlock(blockId) {
        const rows  = document.querySelectorAll('.segment-of-' + blockId);
        const arrow = document.getElementById('arrow-' + blockId);
        const isOpen = arrow.classList.contains('rotate-90');
        rows.forEach(row => row.style.display = isOpen ? 'none' : '');
        arrow.classList.toggle('rotate-90', !isOpen);
        arrow.classList.toggle('rotate-0',  isOpen);
    }

    // ── SORTABLE ──────────────────────────────────────────────────────────
    let sortableInstance = null;

    function initSortable() {
        if (LOCKED) return;
        const tbody = document.getElementById('tabla-segmentos');
        if (!tbody || sortableInstance) return;

        // ── Sortable de SEGMENTOS dentro de cada bloque ───────────────────
        sortableInstance = Sortable.create(tbody, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'opacity-20',
            draggable: '.block-segment',
            onEnd: function() {
                const rows = [...tbody.querySelectorAll('tr')];
                const payload = {};
                let currentBlockId = null;

                rows.forEach(row => {
                    if (row.classList.contains('block-header')) {
                        currentBlockId = row.dataset.blockId;
                        if (!payload[currentBlockId]) payload[currentBlockId] = [];
                    }
                    if (row.classList.contains('block-segment') && currentBlockId) {
                        const segId = row.dataset.segmentId;
                        if (segId) payload[currentBlockId].push(segId);
                    }
                });

                fetch('/rundown/{{ $rundown->id }}/reorder', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: JSON.stringify({ blocks: payload })
                })
                .then(r => r.text())
                .then(html => reloadTabla(html, null));
            }
        });

        // ── Sortable de BLOQUES completos ─────────────────────────────────
        // Al soltar un bloque, enviamos el nuevo orden al servidor.
        // Las letras A/B/C se recalculan solas en el render de table-body.
        Sortable.create(tbody, {
            animation: 150,
            handle: '.block-drag-handle',
            ghostClass: 'opacity-20',
            draggable: '.block-header',
            onEnd: function() {
                const blockIds = [...tbody.querySelectorAll('tr.block-header')]
                    .map(row => row.dataset.blockId);

                fetch('/rundown/{{ $rundown->id }}/reorder-blocks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: JSON.stringify({ block_ids: blockIds })
                })
                .then(r => r.text())
                .then(html => reloadTabla(html, null));
            }
        });
    }

    document.addEventListener('DOMContentLoaded', initSortable);
</script>

{{-- MODAL BIBLIOTECA --}}
<div id="modal-biblioteca" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50"
     onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-gray-800 rounded-xl border border-gray-700 w-full max-w-lg p-6 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold">📚 Biblioteca de Ítems</h2>
                <p class="text-xs text-gray-500 mt-0.5">Haz clic en un ítem para insertarlo en el bloque activo</p>
            </div>
            <button onclick="document.getElementById('modal-biblioteca').classList.add('hidden')"
                class="text-gray-600 hover:text-white transition">✕</button>
        </div>

        {{-- Búsqueda --}}
        <input type="text" id="bib-search" placeholder="Buscar ítem..."
            class="w-full bg-gray-900 border border-gray-700 rounded px-3 py-2 text-sm text-white mb-4 focus:border-blue-500 focus:outline-none">

        {{-- Lista --}}
        <div id="bib-lista" class="flex flex-col gap-2 max-h-80 overflow-y-auto">
            <p class="text-gray-600 text-sm text-center py-4">Cargando...</p>
        </div>
    </div>
</div>

<script>
let _bibItems = [];
let _activeBlockId = null;

window.abrirBiblioteca = async function(blockId) {
    _activeBlockId = blockId || null;
    document.getElementById('modal-biblioteca').classList.remove('hidden');
    document.getElementById('bib-search').value = '';

    const res   = await fetch('/library/segments');
    _bibItems   = await res.json();
    renderBiblioteca(_bibItems);
};

function renderBiblioteca(items) {
    const lista = document.getElementById('bib-lista');
    if (!items.length) {
        lista.innerHTML = '<p class="text-gray-600 text-sm text-center py-4">No hay ítems en la biblioteca.</p>';
        return;
    }
    lista.innerHTML = items.map(item => `
        <div class="flex items-center justify-between bg-gray-900/50 border border-gray-700 rounded px-3 py-2 hover:border-blue-500 transition cursor-pointer group"
             onclick="insertarDeLibreria(${item.id})">
            <div>
                <div class="font-bold text-sm text-white group-hover:text-blue-400 transition">${item.title}</div>
                <div class="text-xs text-gray-500 mt-0.5">${item.type} &nbsp;·&nbsp; ${Math.floor(item.duration_seconds/60)}m ${item.duration_seconds%60}s</div>
            </div>
            <span class="text-xs text-blue-400 opacity-0 group-hover:opacity-100 transition font-bold">+ Insertar</span>
        </div>
    `).join('');
}

document.getElementById('bib-search').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    renderBiblioteca(_bibItems.filter(i => i.title.toLowerCase().includes(q) || i.type.toLowerCase().includes(q)));
});

window.insertarDeLibreria = async function(itemId) {
    if (!_activeBlockId) {
        // Si no hay bloque activo, pedir que seleccione uno
        alert('Selecciona un bloque primero haciendo clic en el botón 📚 de ese bloque.');
        return;
    }
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const res  = await fetch(`/library/segments/${itemId}/insert`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ block_id: _activeBlockId })
    });
    const html = await res.text();
    document.getElementById('tabla-segmentos').innerHTML = html;
    htmx.process(document.getElementById('tabla-segmentos'));
    document.getElementById('modal-biblioteca').classList.add('hidden');
};
</script>

</body>
</html>
