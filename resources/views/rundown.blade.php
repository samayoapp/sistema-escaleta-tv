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
</head>
<body class="bg-gray-900 text-white font-sans p-6">

<div class="max-w-7xl mx-auto">

    {{-- HEADER --}}
    <header class="flex justify-between items-center mb-8 border-b border-gray-700 pb-4">
        <div>
            <h1 class="text-3xl font-bold text-blue-400">{{ $rundown->show->title }}</h1>
            <p class="text-gray-400 text-sm">Fecha de emisión: {{ $rundown->air_date }}</p>
        </div>
        <div class="flex gap-2">
            <a href="/rundown/{{ $rundown->id }}/pdf" target="_blank"
                class="bg-gray-600 hover:bg-gray-500 px-4 py-2 rounded text-sm font-bold uppercase transition">
                📄 Descargar Guion PDF
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

            {{-- Tiempo total --}}
            <div id="total-duration"
                 class="bg-gray-800 p-4 rounded-lg border border-gray-700 text-right"
                 hx-get="/rundown/{{ $rundown->id }}/get-time"
                 hx-trigger="refreshTime from:body">
                @include('partials.total-time', ['rundown' => $rundown])
            </div>

            {{-- Tabla de bloques y segmentos --}}
            <div class="bg-gray-800 rounded-lg shadow-2xl overflow-hidden border border-gray-700">

                {{-- Toolbar --}}
                <div class="p-4 bg-gray-700/50 flex justify-between items-center border-b border-gray-700">
                    <h2 class="text-xs font-bold uppercase text-gray-400 tracking-widest">
                        Estructura del Programa
                    </h2>
                    <button
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

                {{-- Tabla --}}
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-700/30 text-gray-400 uppercase text-xs border-b border-gray-700">
                            <th class="px-4 py-3 w-10"></th>
                            <th class="px-4 py-3 w-12">#</th>
                            <th class="px-4 py-3">Título / Tipo</th>
                            <th class="px-4 py-3 w-32">Duración</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-segmentos" class="divide-y divide-gray-700/30">
                        @include('partials.table-body', ['rundown' => $rundown])
                    </tbody>
                </table>
            </div>
        </div>

        {{-- COLUMNA DERECHA: Editor de guion --}}
        <div id="editor-container"
             class="bg-gray-800 rounded-lg p-6 shadow-2xl border border-gray-700 min-h-[500px] self-start sticky top-6">
            <div class="flex flex-col items-center justify-center h-64 text-gray-600 italic text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <p class="text-sm">Selecciona un segmento para editar su guion literario.</p>
            </div>
        </div>

    </div>
</div>
<script>
    // CSRF para HTMX
    document.body.addEventListener('htmx:configRequest', (event) => {
        event.detail.headers['X-CSRF-Token'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    });

    // Collapse/Expand de bloques
    function toggleBlock(blockId) {
        const rows = document.querySelectorAll('.segment-of-' + blockId);
        const arrow = document.getElementById('arrow-' + blockId);
        const isOpen = arrow.classList.contains('rotate-90');
        rows.forEach(row => row.style.display = isOpen ? 'none' : '');
        arrow.classList.toggle('rotate-90', !isOpen);
        arrow.classList.toggle('rotate-0', isOpen);
    }

    // Sortable — un solo Sortable en todo el tbody
    let sortableInstance = null;

    function initSortable() {
        const tbody = document.getElementById('tabla-segmentos');
        if (!tbody || sortableInstance) return;

        sortableInstance = Sortable.create(tbody, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'opacity-20',
            draggable: '.block-segment',  // Solo arrastra filas de segmentos, no cabeceras
            onEnd: function(evt) {
                // Reconstruimos el payload leyendo el DOM en orden visual
                // Para cada segmento visible, leemos a qué bloque pertenece su cabecera más cercana
                const rows = [...tbody.querySelectorAll('tr')];
                const payload = {}; // { blockId: [segId, segId, ...] }
                let currentBlockId = null;

                rows.forEach(row => {
                    // Si es cabecera de bloque, actualizamos el bloque actual
                    if (row.classList.contains('block-header')) {
                        currentBlockId = row.dataset.blockId;
                        if (!payload[currentBlockId]) payload[currentBlockId] = [];
                    }
                    // Si es segmento, lo asignamos al bloque actual
                    if (row.classList.contains('block-segment') && currentBlockId) {
                        const segId = row.dataset.segmentId;
                        if (segId) payload[currentBlockId].push(segId);
                    }
                });

                // Enviamos al servidor
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

    // Reinicializar después de swaps de HTMX
    document.addEventListener('htmx:afterSwap', function(e) {
        if (e.detail.target.id === 'tabla-segmentos') {
            sortableInstance = null;
            initSortable();
        }
    });
</script>

</body>
</html>