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
        <header class="flex justify-between items-center mb-10 border-b border-gray-700 pb-4">
            <div>
                <h1 class="text-3xl font-bold text-blue-400">{{ $rundown->show->title }}</h1>
                <p class="text-gray-400">Fecha de emisión: {{ $rundown->air_date }}</p>
            </div>
            <div class="flex gap-2">
                <a href="/rundown/{{ $rundown->id }}/prompter" target="_blank" 
                class="bg-yellow-600 hover:bg-yellow-500 px-4 py-2 rounded text-sm font-bold uppercase transition">
                    📺 Abrir Teleprompter
                </a>
                <div class="bg-green-600 px-4 py-2 rounded text-sm font-bold uppercase">
                    En Producción
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 flex flex-col gap-6">
                
                <div 
                    id="total-duration" 
                    class="bg-gray-800 p-4 rounded-lg border border-gray-700 text-right shadow-inner"
                    hx-get="/rundown/{{ $rundown->id }}/get-time" 
                    hx-trigger="refreshTime from:body"
                >
                    @include('partials.total-time', ['rundown' => $rundown])
                </div>

                <div class="bg-gray-800 rounded-lg shadow-2xl overflow-hidden border border-gray-700">
                    <div class="p-4 bg-gray-700 flex justify-between items-center">
                        <h2 class="text-sm font-bold uppercase text-gray-400 font-bold">Estructura del Programa</h2>
                        <button 
                            hx-post="/rundown/{{ $rundown->id }}/add-segment" 
                            hx-target="#tabla-segmentos" 
                            hx-swap="beforeend"
                            class="bg-green-600 hover:bg-green-500 text-white text-xs font-bold py-2 px-4 rounded transition flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            NUEVO SEGMENTO
                        </button>
                    </div>

                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-700 text-gray-300 uppercase text-xs border-b border-gray-700">
                                <th class="px-4 py-3 w-10"></th> <th class="px-6 py-3 w-20">Orden</th>
                                <th class="px-6 py-3">Título del Bloque</th>
                                <th class="px-6 py-3 w-32">Duración</th>
                                <th class="px-6 py-3 text-right text-blue-400 font-bold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-segmentos" 
                            class="divide-y divide-gray-700"
                            hx-post="/rundown/{{ $rundown->id }}/reorder" 
                            hx-trigger="ordenActualizado"
                            hx-include="[name='segment_ids[]']">
                            @include('partials.table-body', ['rundown' => $rundown])
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="editor-container" class="bg-gray-800 rounded-lg p-6 shadow-2xl border border-gray-700 min-h-[500px] self-start">
                <div class="flex flex-col items-center justify-center h-full text-gray-500 italic text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <p>Selecciona un segmento de la izquierda para editar su guion literario.</p>
                </div>
            </div>
        </div> 
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('htmx:configRequest', (event) => {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                event.detail.headers['X-CSRF-Token'] = token;
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('tabla-segmentos');
            Sortable.create(el, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'bg-blue-900',
                onEnd: function () {
                    // Seleccionamos los valores de los inputs dentro de la tabla
                    const values = htmx.values(el);
                    
                    // Forzamos que la respuesta solo reemplace el contenido del TBODY
                    htmx.ajax('POST', '/rundown/{{ $rundown->id }}/reorder', {
                        values: values,
                        target: '#tabla-segmentos', // <--- ESTO EVITA QUE SE BORRE EL RESTO
                        swap: 'innerHTML'           // <--- SOLO CAMBIA LO DE ADENTRO
                    });
                }
            });
        });
    </script>
</body>
</html>