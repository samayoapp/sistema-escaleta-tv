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
<body class="bg-gray-900 text-white font-sans p-6">

<div class="max-w-7xl mx-auto">

    {{-- HEADER --}}
    <header class="flex justify-between items-center mb-8 border-b border-gray-700 pb-4">
        <div>
            <h1 class="text-3xl font-bold text-blue-400">{{ $rundown->show->title }}</h1>
            <p class="text-gray-400 text-sm">
                Fecha: {{ $rundown->air_date }} &nbsp;·&nbsp;
                Inicio:
                <input
                    type="time"
                    name="air_time"
                    value="{{ substr($rundown->air_time ?? '19:00:00', 0, 5) }}"
                    hx-post="/rundown/{{ $rundown->id }}/update-time"
                    hx-trigger="blur"
                    hx-target="#tabla-segmentos"
                    hx-swap="innerHTML"
                    hx-include="this"
                    class="bg-transparent border-b border-gray-600 text-yellow-400 font-mono text-sm focus:outline-none focus:border-yellow-400 cursor-pointer">
            </p>
        </div>
        <div class="flex gap-2 items-center flex-wrap">
            <a href="/shows/{{ $rundown->show_id }}"
                class="text-gray-500 hover:text-white transition mr-2">
                ← Volver
            </a>
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
            <div class="bg-green-700 px-4 py-2 rounded text-sm font-bold uppercase">
                🔴 En Producción
            </div>
        </div>
    </header>

    {{-- LAYOUT PRINCIPAL --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- COLUMNA IZQUIERDA: Tabla --}}
        <div class="lg:col-span-2 flex flex-col gap-4">

            <div id="total-duration"
                 class="bg-gray-800 p-4 rounded-lg border border-gray-700 text-right"
                 hx-get="/rundown/{{ $rundown->id }}/get-time"
                 hx-trigger="refreshTime from:body">
                @include('partials.total-time', ['rundown' => $rundown])
            </div>

            <div class="bg-gray-800 rounded-lg shadow-2xl overflow-hidden border border-gray-700">
                <div class="p-4 bg-gray-700/50 flex justify-between items-center border-b border-gray-700">
                    <h2 class="text-xs font-bold uppercase text-gray-400 tracking-widest">
                        Estructura del Programa
                    </h2>
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
                        @include('partials.table-body', ['rundown' => $rundown])
                    </tbody>
                </table>
            </div>
        </div>

        {{-- COLUMNA DERECHA: Panel de propiedades --}}
        <div id="editor-container"
             class="bg-gray-800 rounded-lg p-5 shadow-2xl border border-gray-700 self-start sticky top-6 transition-all">
            <div class="flex flex-col items-center justify-center h-64 text-gray-600 italic text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-3 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/>
                </svg>
                <p class="text-sm">Haz clic en un ítem<br>para ver sus propiedades.</p>
            </div>
        </div>

    </div>
</div>

<script>
    // ── CSRF ──────────────────────────────────────────────────────────────
    document.body.addEventListener('htmx:configRequest', (event) => {
        event.detail.headers['X-CSRF-Token'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    });

    // ── BANDERAS para foco en nuevo ítem ──────────────────────────────────
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

    // ── SELECCIÓN ─────────────────────────────────────────────────────────
    let selectedSegmentId = null;

    function seleccionarSegmento(segmentId, row) {
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

    // ── AFTER SWAP ────────────────────────────────────────────────────────
    document.addEventListener('htmx:afterSwap', function(e) {
        if (e.detail.target.id !== 'tabla-segmentos') return;

        sortableInstance = null;
        initSortable();

        // Restaurar selección visual
        if (selectedSegmentId) {
            const row = document.getElementById('segment-' + selectedSegmentId);
            if (row) row.classList.add('segment-selected');
        }

        // Solo enfocar si se agregó ítem/bloque nuevo
        if (justAddedItem) {
            justAddedItem = false;
            setTimeout(() => {
                if (addedToBlockId) {
                    // Último ítem del bloque específico donde se agregó
                    const segRows = document.querySelectorAll(
                        `#tabla-segmentos tr.segment-of-${addedToBlockId}`
                    );
                    if (segRows.length > 0) {
                        const lastInput = segRows[segRows.length - 1].querySelector('input.seg-title-input');
                        if (lastInput) {
                            lastInput.focus();
                            lastInput.select();
                            lastInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            addedToBlockId = null;
                            return;
                        }
                    }
                }
                // Fallback: nuevo bloque vacío — enfocar input del último bloque
                const blockInputs = document.querySelectorAll('#tabla-segmentos .block-header input[name="title"]');
                if (blockInputs.length > 0) {
                    const last = blockInputs[blockInputs.length - 1];
                    last.focus();
                    last.select();
                    last.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 80);
        } // ← cierre del if(justAddedItem)
    }); // ← cierre del addEventListener

    // ── COLLAPSE / EXPAND ─────────────────────────────────────────────────
    function toggleBlock(blockId) {
        const rows = document.querySelectorAll('.segment-of-' + blockId);
        const arrow = document.getElementById('arrow-' + blockId);
        const isOpen = arrow.classList.contains('rotate-90');
        rows.forEach(row => row.style.display = isOpen ? 'none' : '');
        arrow.classList.toggle('rotate-90', !isOpen);
        arrow.classList.toggle('rotate-0', isOpen);
    }

    // ── SORTABLE ──────────────────────────────────────────────────────────
    let sortableInstance = null;

    function initSortable() {
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ blocks: payload })
                })
                .then(r => r.text())
                .then(html => {
                    tbody.innerHTML = html;
                    sortableInstance = null;
                    initSortable();
                    htmx.trigger(document.body, 'refreshTime');
                });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', initSortable);
</script>

</body>
</html>
