<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producción TV — Catálogo de Programas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-900 text-white font-sans min-h-screen">

<div class="max-w-6xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <header class="flex justify-between items-center mb-10 border-b border-gray-700 pb-6">
        <div>
            <h1 class="text-3xl font-bold text-blue-400 tracking-wide">📺 Producción TV</h1>
            <p class="text-gray-500 text-sm mt-1">Catálogo de Programas</p>
        </div>
        {{-- Botón nuevo show --}}
        <button onclick="document.getElementById('modal-nuevo-show').classList.remove('hidden')"
            class="bg-blue-600 hover:bg-blue-500 px-5 py-2 rounded text-sm font-bold uppercase tracking-widest transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Programa
        </button>
    </header>

    {{-- SHOWS ACTIVOS --}}
    @php $activos = $shows->where('status', 'active'); @endphp
    @if($activos->count() > 0)
        <div class="mb-10">
            <h2 class="text-xs font-bold uppercase text-gray-500 tracking-widest mb-4">
                Programas Activos
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($activos as $show)
                    <div class="bg-gray-800 rounded-lg border border-gray-700 hover:border-blue-600 transition group">
                        
                        <a href="/shows/{{ $show->id }}" class="block p-5">
                            <div class="flex items-start justify-between mb-3">
                                <div class="bg-blue-600/20 text-blue-400 p-2 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <span class="text-[10px] font-bold uppercase text-green-400 bg-green-400/10 px-2 py-1 rounded">
                                    Activo
                                </span>
                            </div>
                            <h3 class="text-lg font-bold text-white group-hover:text-blue-400 transition mb-1">
                                {{ $show->title }}
                            </h3>
                            @if($show->channel)
                                <p class="text-xs text-gray-500 mb-2">📡 {{ $show->channel }}</p>
                            @endif
                            @if($show->description)
                                <p class="text-xs text-gray-400 mb-3 line-clamp-2">{{ $show->description }}</p>
                            @endif
                            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-700">
                                <span class="text-xs text-gray-500">
                                    {{ $show->rundowns_count }} 
                                    {{ $show->rundowns_count == 1 ? 'escaleta' : 'escaletas' }}
                                </span>
                                <span class="text-xs text-blue-400 font-bold group-hover:underline">
                                    Ver escaletas →
                                </span>
                            </div>
                        </a>

                        {{-- Acciones --}}
                        <div class="px-5 pb-4 flex gap-2">
                            <button onclick="abrirEditar({{ $show->id }}, '{{ addslashes($show->title) }}', '{{ addslashes($show->channel ?? '') }}', '{{ addslashes($show->description ?? '') }}')"
                                class="text-xs text-gray-500 hover:text-white transition">
                                ✏️ Editar
                            </button>
                            <form method="POST" action="/shows/{{ $show->id }}/archive" class="inline">
                                @csrf
                                <button type="submit" class="text-xs text-gray-500 hover:text-yellow-400 transition">
                                    📦 Archivar
                                </button>
                            </form>
                            <form method="POST" action="/shows/{{ $show->id }}"
                                onsubmit="return confirm('¿Eliminar este programa y TODAS sus escaletas? No se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-gray-500 hover:text-red-400 transition">
                                    🗑 Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="text-center py-20 text-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            <p class="text-lg italic">No hay programas. ¡Crea el primero!</p>
        </div>
    @endif

    {{-- SHOWS ARCHIVADOS --}}
    @php $archivados = $shows->where('status', 'archived'); @endphp
    @if($archivados->count() > 0)
        <div class="mt-8">
            <h2 class="text-xs font-bold uppercase text-gray-600 tracking-widest mb-4">
                Programas Archivados
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($archivados as $show)
                    <div class="bg-gray-800/50 rounded-lg border border-gray-800 opacity-60 hover:opacity-100 transition">
                        <a href="/shows/{{ $show->id }}" class="block p-5">
                            <div class="flex items-start justify-between mb-3">
                                <h3 class="text-base font-bold text-gray-400">{{ $show->title }}</h3>
                                <span class="text-[10px] font-bold uppercase text-gray-500 bg-gray-700 px-2 py-1 rounded">
                                    Archivado
                                </span>
                            </div>
                            <p class="text-xs text-gray-600">{{ $show->rundowns_count }} escaletas</p>
                        </a>
                        <div class="px-5 pb-4">
                            <form method="POST" action="/shows/{{ $show->id }}/archive" class="inline">
                                @csrf
                                <button type="submit" class="text-xs text-gray-600 hover:text-green-400 transition">
                                    ♻️ Reactivar
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>

{{-- MODAL NUEVO SHOW --}}
<div id="modal-nuevo-show" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
    <div class="bg-gray-800 rounded-xl border border-gray-700 w-full max-w-md p-6 shadow-2xl">
        <h2 class="text-lg font-bold text-white mb-5">Nuevo Programa</h2>
        <form method="POST" action="/shows">
            @csrf
            <div class="flex flex-col gap-4">
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Nombre del Programa *</label>
                    <input type="text" name="title" required placeholder="Ej: Noticiero Central"
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Canal</label>
                    <input type="text" name="channel" placeholder="Ej: Canal 11"
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Descripción</label>
                    <textarea name="description" rows="2" placeholder="Descripción breve del programa..."
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none resize-none"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('modal-nuevo-show').classList.add('hidden')"
                    class="px-4 py-2 text-sm text-gray-400 hover:text-white transition">
                    Cancelar
                </button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-500 px-5 py-2 rounded text-sm font-bold uppercase transition">
                    Crear Programa
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDITAR SHOW --}}
<div id="modal-editar-show" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
    <div class="bg-gray-800 rounded-xl border border-gray-700 w-full max-w-md p-6 shadow-2xl">
        <h2 class="text-lg font-bold text-white mb-5">Editar Programa</h2>
        <form method="POST" id="form-editar-show" action="">
            @csrf
            <div class="flex flex-col gap-4">
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Nombre del Programa *</label>
                    <input type="text" name="title" id="edit-title" required
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Canal</label>
                    <input type="text" name="channel" id="edit-channel"
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Descripción</label>
                    <textarea name="description" id="edit-description" rows="2"
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none resize-none"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('modal-editar-show').classList.add('hidden')"
                    class="px-4 py-2 text-sm text-gray-400 hover:text-white transition">
                    Cancelar
                </button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-500 px-5 py-2 rounded text-sm font-bold uppercase transition">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirEditar(id, title, channel, description) {
        document.getElementById('edit-title').value = title;
        document.getElementById('edit-channel').value = channel;
        document.getElementById('edit-description').value = description;
        document.getElementById('form-editar-show').action = '/shows/' + id + '/update';
        document.getElementById('modal-editar-show').classList.remove('hidden');
    }

    // Cerrar modales con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.getElementById('modal-nuevo-show').classList.add('hidden');
            document.getElementById('modal-editar-show').classList.add('hidden');
        }
    });
</script>

</body>
</html>
