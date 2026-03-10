<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rundown - {{ $rundown->show->title }}</title>
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
    $tz = 'America/Tegucigalpa';
    // Parsear fecha/hora de la escaleta EN timezone local
    $airDateTime = \Carbon\Carbon::createFromFormat(
        'Y-m-d H:i:s',
        $rundown->air_date . ' ' . ($rundown->air_time ?? '00:00:00'),
        $tz
    );
    // "now" también en Tegucigalpa para comparar manzanas con manzanas
    $nowLocal = \Carbon\Carbon::now($tz);
    // Bloqueado cuando ya pasó 1 hora desde la hora de emisión
    $locked = $nowLocal->greaterThan($airDateTime->copy()->addHour());
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
    <header class="flex justify-between items-center mb-8 border-b border-gray-700 pb-4 {{ $locked ? 'opacity-60' : '' }}">
        <div>
            <div class="flex items-center gap-4 mb-1">
                <h1 class="text-3xl font-bold {{ $locked ? 'text-gray-500' : 'text-blue-400' }}">
                    {{ $rundown->show->title }}
                </h1>
                {{-- RELOJ EN TIEMPO REAL GMT-6 --}}
                <div class="flex flex-col items-start">
                    <div id="reloj-hora"
                         class="font-mono font-bold text-2xl {{ $locked ? 'text-gray-600' : 'text-yellow-400' }} leading-none tabular-nums">
                        --:--:--
                    </div>
                    <div class="text-[10px] text-gray-600 uppercase tracking-widest mt-0.5">
                        Tegucigalpa · GMT-6
                    </div>
                </div>
            </div>
            <p class="{{ $locked ? 'text-gray-600' : 'text-gray-400' }} text-sm">
                Fecha: {{ $rundown->air_date }} &nbsp;·&nbsp;
                Inicio: {{ substr($rundown->air_time ?? '00:00:00', 0, 5) }}
            </p>
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
        tbody.innerHTML = html;

        // CRÍTICO: registrar todos los atributos hx-* de los nuevos elementos
        htmx.process(tbody);

        sortableInstance = null;
        initSortable();
        htmx.trigger(document.body, 'refreshTime');

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

    // ── AFTER SWAP — solo para operaciones HTMX normales ─────────────────
    document.addEventListener('htmx:afterSwap', function(e) {
        if (e.detail.target.id !== 'tabla-segmentos') return;

        // CRÍTICO: registrar atributos hx-* de los nuevos elementos
        htmx.process(e.detail.target);

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
    }

    document.addEventListener('DOMContentLoaded', initSortable);
</script>

</body>
</html>
