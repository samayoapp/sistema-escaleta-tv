<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración Global — RONUP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-900 text-white font-sans min-h-screen">

@include('partials.navbar')

<div class="max-w-6xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <header class="flex items-center justify-between mb-8 border-b border-gray-700 pb-6">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-wide">⚙️ Configuración Global</h1>
            <p class="text-gray-500 text-sm mt-1">Tipos de ítem y tipos de producción</p>
        </div>
        <a href="/" class="text-gray-500 hover:text-white text-sm transition">← Catálogo</a>
    </header>

    {{-- FLASH --}}
    @if(session('success'))
        <div class="mb-6 bg-green-900/30 border border-green-700 text-green-400 text-sm px-4 py-3 rounded">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-red-900/30 border border-red-700 text-red-400 text-sm px-4 py-3 rounded">✗ {{ session('error') }}</div>
    @endif

    {{-- ══ SECCIÓN 1: TIPOS DE ÍTEM ══ --}}
    <section id="segment-types" class="mb-14">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-lg font-bold text-white">🎛 Tipos de Ítem</h2>
                <p class="text-xs text-gray-500 mt-0.5">Catálogo global — actívalos por tipo de producción con los toggles</p>
            </div>
            <button onclick="document.getElementById('modal-nuevo-item').classList.remove('hidden')"
                class="bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded text-sm font-bold uppercase tracking-widest transition">
                + Nuevo Tipo de Ítem
            </button>
        </div>

        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-x-auto">
            <table class="w-full min-w-max">
                <thead>
                    <tr class="border-b border-gray-700 text-xs uppercase text-gray-500 tracking-widest">
                        <th class="px-4 py-3 text-left">Ítem</th>
                        <th class="px-4 py-3 text-left">Clave</th>
                        <th class="px-4 py-3 text-center">Color</th>
                        @foreach($productionTypes as $pt)
                        <th class="px-4 py-3 text-center min-w-[120px]">{{ $pt->icon }} {{ $pt->label }}</th>
                        @endforeach
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/40">
                    @forelse($segmentTypes as $st)
                    <tr class="hover:bg-gray-700/20 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">{{ $st->icon }}</span>
                                <span class="font-bold text-sm" style="color: {{ $st->color_hex }}">{{ $st->label }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs bg-gray-900 px-2 py-1 rounded text-gray-400">{{ $st->value }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <div class="w-4 h-4 rounded-full border border-gray-600" style="background-color: {{ $st->color_hex }}"></div>
                                <span class="text-xs font-mono text-gray-600">{{ $st->color_hex }}</span>
                            </div>
                        </td>
                        @foreach($productionTypes as $pt)
                        @php $isActive = $pivotMap[$pt->id][$st->id] ?? false; @endphp
                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="/admin/config/pivot/{{ $pt->id }}/{{ $st->id }}/toggle" class="inline">
                                @csrf
                                <button type="submit"
                                    class="w-11 h-6 rounded-full transition-all duration-200 relative inline-flex items-center
                                        {{ $isActive ? 'bg-blue-600' : 'bg-gray-700' }}">
                                    <span class="absolute w-5 h-5 rounded-full bg-white shadow transition-all duration-200
                                        {{ $isActive ? 'left-[22px]' : 'left-[2px]' }}"></span>
                                </button>
                            </form>
                        </td>
                        @endforeach
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <button onclick="abrirEditarItem({{ $st->id }}, '{{ addslashes($st->label) }}', '{{ $st->icon }}', '{{ $st->color_hex }}', {{ $st->order_index }})"
                                    class="text-xs text-gray-500 hover:text-white px-2 py-1 rounded hover:bg-gray-700 transition">✏️</button>
                                <form method="POST" action="/admin/config/segment-types/{{ $st->id }}/delete"
                                    onsubmit="return confirm('¿Eliminar {{ addslashes($st->label) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-gray-600 hover:text-red-400 px-2 py-1 rounded hover:bg-gray-700 transition">🗑</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="{{ 4 + $productionTypes->count() }}" class="px-4 py-8 text-center text-gray-600 italic text-sm">No hay tipos de ítem.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- ══ SECCIÓN 2: TIPOS DE PRODUCCIÓN ══ --}}
    <section id="production-types">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-lg font-bold text-white">📺 Tipos de Producción</h2>
                <p class="text-xs text-gray-500 mt-0.5">Define los formatos y sus condiciones de comportamiento</p>
            </div>
            <button onclick="document.getElementById('modal-nuevo-pt').classList.remove('hidden')"
                class="bg-purple-600 hover:bg-purple-500 px-4 py-2 rounded text-sm font-bold uppercase tracking-widest transition">
                + Nuevo Tipo
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($productionTypes as $pt)
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 {{ $pt->active ? '' : 'opacity-50' }}">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">{{ $pt->icon }}</span>
                        <div>
                            <div class="font-bold text-white">{{ $pt->label }}</div>
                            <div class="font-mono text-xs text-gray-500">{{ $pt->value }}</div>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold uppercase px-2 py-1 rounded {{ $pt->active ? 'bg-green-900/30 text-green-400' : 'bg-gray-700 text-gray-500' }}">
                        {{ $pt->active ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
                <div class="flex flex-wrap gap-1.5 mb-3">
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $pt->has_air_time ? 'bg-blue-900/40 text-blue-400' : 'bg-gray-700/50 text-gray-600' }}">
                        {{ $pt->has_air_time ? '✓' : '✗' }} Hora de Inicio
                    </span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $pt->has_lock ? 'bg-red-900/40 text-red-400' : 'bg-gray-700/50 text-gray-600' }}">
                        {{ $pt->has_lock ? '✓' : '✗' }} Bloqueo Auto
                    </span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $pt->has_episode ? 'bg-pink-900/40 text-pink-400' : 'bg-gray-700/50 text-gray-600' }}">
                        {{ $pt->has_episode ? '✓' : '✗' }} Episodio
                    </span>
                </div>
                <div class="text-xs text-gray-500 mb-4">
                    {{ $pt->segmentTypes->where('pivot.active', true)->count() }} tipos de ítem activos
                </div>
                <div class="flex gap-2 pt-3 border-t border-gray-700">
                    <button onclick="abrirEditarPT({{ $pt->id }}, '{{ addslashes($pt->label) }}', '{{ $pt->icon }}', {{ $pt->has_air_time ? 'true' : 'false' }}, {{ $pt->has_lock ? 'true' : 'false' }}, {{ $pt->has_episode ? 'true' : 'false' }}, {{ $pt->order_index }})"
                        class="flex-1 text-xs text-gray-400 hover:text-white bg-gray-700 hover:bg-gray-600 px-3 py-1.5 rounded transition text-center">
                        ✏️ Editar
                    </button>
                    <form method="POST" action="/admin/config/production-types/{{ $pt->id }}/toggle" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full text-xs font-bold px-3 py-1.5 rounded transition
                            {{ $pt->active ? 'text-gray-500 bg-gray-700 hover:bg-red-900/30 hover:text-red-400' : 'text-green-400 bg-green-900/20 hover:bg-green-900/40' }}">
                            {{ $pt->active ? 'Desactivar' : 'Activar' }}
                        </button>
                    </form>
                    @if(!in_array($pt->value, ['live', 'reality']))
                    <form method="POST" action="/admin/config/production-types/{{ $pt->id }}/delete"
                        onsubmit="return confirm('¿Eliminar {{ addslashes($pt->label) }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-gray-600 hover:text-red-400 bg-gray-700 hover:bg-gray-600 px-3 py-1.5 rounded transition">🗑</button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </section>
</div>

{{-- MODAL NUEVO ÍTEM --}}
<div id="modal-nuevo-item" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50" onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-gray-800 rounded-xl border border-gray-700 w-full max-w-md p-6 shadow-2xl">
        <div class="flex justify-between mb-5"><h2 class="text-lg font-bold">➕ Nuevo Tipo de Ítem</h2><button onclick="document.getElementById('modal-nuevo-item').classList.add('hidden')" class="text-gray-600 hover:text-white">✕</button></div>
        <form method="POST" action="/admin/config/segment-types">
            @csrf
            <div class="flex flex-col gap-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Ícono *</label>
                        <input type="text" name="icon" id="ni-icon" maxlength="5" placeholder="🎬" class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white text-2xl text-center focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Color *</label>
                        <div class="flex gap-2 items-center mt-1">
                            <input type="color" name="color_hex" id="ni-color" value="#3b82f6" class="w-10 h-10 rounded border border-gray-600 cursor-pointer p-0.5">
                            <span id="ni-color-hex" class="text-xs font-mono text-gray-400">#3b82f6</span>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Nombre visible *</label>
                    <input type="text" name="label" id="ni-label" placeholder="ENTREVISTA" class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white uppercase focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Clave interna * <span class="text-gray-600 normal-case font-normal">MAYÚSCULAS y _</span></label>
                    <input type="text" name="value" placeholder="ENTREVISTA" class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white font-mono uppercase focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-2">Activo en tipos de producción</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($productionTypes as $pt)
                        <label class="flex items-center gap-1.5 cursor-pointer bg-gray-900 border border-gray-600 rounded px-3 py-1.5 hover:border-blue-500 transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-900/20">
                            <input type="checkbox" name="production_type_ids[]" value="{{ $pt->id }}" class="accent-blue-500">
                            <span class="text-sm">{{ $pt->icon }} {{ $pt->label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Orden</label>
                    <input type="number" name="order_index" value="99" min="0" class="w-24 bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div class="bg-gray-900/50 border border-gray-700 rounded p-3 flex items-center gap-3">
                    <span id="ni-prev-icon" class="text-xl">🎬</span>
                    <span id="ni-prev-label" class="font-bold text-sm" style="color:#3b82f6">NUEVO TIPO</span>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-5">
                <button type="button" onclick="document.getElementById('modal-nuevo-item').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-400 hover:text-white">Cancelar</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 px-5 py-2 rounded text-sm font-bold uppercase">Crear</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDITAR ÍTEM --}}
<div id="modal-editar-item" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50" onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-gray-800 rounded-xl border border-gray-700 w-full max-w-md p-6 shadow-2xl">
        <div class="flex justify-between mb-5"><h2 class="text-lg font-bold">✏️ Editar Tipo de Ítem</h2><button onclick="document.getElementById('modal-editar-item').classList.add('hidden')" class="text-gray-600 hover:text-white">✕</button></div>
        <form method="POST" id="form-ei" action="">
            @csrf
            <div class="flex flex-col gap-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Ícono *</label>
                        <input type="text" name="icon" id="ei-icon" maxlength="5" class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white text-2xl text-center focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Color *</label>
                        <div class="flex gap-2 items-center mt-1">
                            <input type="color" name="color_hex" id="ei-color" class="w-10 h-10 rounded border border-gray-600 cursor-pointer p-0.5">
                            <span id="ei-color-hex" class="text-xs font-mono text-gray-400"></span>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Nombre visible *</label>
                    <input type="text" name="label" id="ei-label" class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white uppercase focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Orden</label>
                    <input type="number" name="order_index" id="ei-order" min="0" class="w-24 bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div class="bg-gray-900/50 border border-gray-700 rounded p-3 flex items-center gap-3">
                    <span id="ei-prev-icon" class="text-xl"></span>
                    <span id="ei-prev-label" class="font-bold text-sm"></span>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-5">
                <button type="button" onclick="document.getElementById('modal-editar-item').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-400 hover:text-white">Cancelar</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 px-5 py-2 rounded text-sm font-bold uppercase">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL NUEVO TIPO DE PRODUCCIÓN --}}
<div id="modal-nuevo-pt" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50" onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-gray-800 rounded-xl border border-gray-700 w-full max-w-md p-6 shadow-2xl">
        <div class="flex justify-between mb-5"><h2 class="text-lg font-bold">📺 Nuevo Tipo de Producción</h2><button onclick="document.getElementById('modal-nuevo-pt').classList.add('hidden')" class="text-gray-600 hover:text-white">✕</button></div>
        <form method="POST" action="/admin/config/production-types">
            @csrf
            <div class="flex flex-col gap-4">
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Ícono *</label>
                        <input type="text" name="icon" maxlength="5" placeholder="🎥" class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white text-2xl text-center focus:border-purple-500 focus:outline-none">
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Nombre *</label>
                        <input type="text" name="label" placeholder="Documental" class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-purple-500 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Clave interna * <span class="text-gray-600 normal-case font-normal">minúsculas y _</span></label>
                    <input type="text" name="value" placeholder="documental" class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white font-mono lowercase focus:border-purple-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-2">Condiciones de comportamiento</label>
                    <div class="flex flex-col gap-2">
                        <label class="flex items-center gap-3 bg-gray-900/50 border border-gray-700 rounded px-3 py-2 cursor-pointer hover:border-blue-500 transition">
                            <input type="checkbox" name="has_air_time" value="1" class="accent-blue-500">
                            <div><div class="text-sm font-bold text-blue-400">🕐 Hora de Inicio</div><div class="text-xs text-gray-500">La escaleta tiene hora de emisión al aire</div></div>
                        </label>
                        <label class="flex items-center gap-3 bg-gray-900/50 border border-gray-700 rounded px-3 py-2 cursor-pointer hover:border-red-500 transition">
                            <input type="checkbox" name="has_lock" value="1" class="accent-red-500">
                            <div><div class="text-sm font-bold text-red-400">🔒 Bloqueo Automático</div><div class="text-xs text-gray-500">Se bloquea 1h después de la hora de aire</div></div>
                        </label>
                        <label class="flex items-center gap-3 bg-gray-900/50 border border-gray-700 rounded px-3 py-2 cursor-pointer hover:border-pink-500 transition">
                            <input type="checkbox" name="has_episode" value="1" class="accent-pink-500">
                            <div><div class="text-sm font-bold text-pink-400">🎬 Episodios</div><div class="text-xs text-gray-500">Cada escaleta tiene nombre y número de episodio</div></div>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Orden</label>
                    <input type="number" name="order_index" value="99" min="0" class="w-24 bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-purple-500 focus:outline-none">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-5">
                <button type="button" onclick="document.getElementById('modal-nuevo-pt').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-400 hover:text-white">Cancelar</button>
                <button type="submit" class="bg-purple-600 hover:bg-purple-500 px-5 py-2 rounded text-sm font-bold uppercase">Crear Tipo</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDITAR TIPO DE PRODUCCIÓN --}}
<div id="modal-editar-pt" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50" onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-gray-800 rounded-xl border border-gray-700 w-full max-w-md p-6 shadow-2xl">
        <div class="flex justify-between mb-5"><h2 class="text-lg font-bold">✏️ Editar Tipo de Producción</h2><button onclick="document.getElementById('modal-editar-pt').classList.add('hidden')" class="text-gray-600 hover:text-white">✕</button></div>
        <form method="POST" id="form-ept" action="">
            @csrf
            <div class="flex flex-col gap-4">
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Ícono *</label>
                        <input type="text" name="icon" id="ept-icon" maxlength="5" class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white text-2xl text-center focus:border-purple-500 focus:outline-none">
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Nombre *</label>
                        <input type="text" name="label" id="ept-label" class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-purple-500 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-2">Condiciones</label>
                    <div class="flex flex-col gap-2">
                        <label class="flex items-center gap-3 bg-gray-900/50 border border-gray-700 rounded px-3 py-2 cursor-pointer hover:border-blue-500 transition">
                            <input type="checkbox" name="has_air_time" id="ept-air" value="1" class="accent-blue-500">
                            <div><div class="text-sm font-bold text-blue-400">🕐 Hora de Inicio</div></div>
                        </label>
                        <label class="flex items-center gap-3 bg-gray-900/50 border border-gray-700 rounded px-3 py-2 cursor-pointer hover:border-red-500 transition">
                            <input type="checkbox" name="has_lock" id="ept-lock" value="1" class="accent-red-500">
                            <div><div class="text-sm font-bold text-red-400">🔒 Bloqueo Automático</div></div>
                        </label>
                        <label class="flex items-center gap-3 bg-gray-900/50 border border-gray-700 rounded px-3 py-2 cursor-pointer hover:border-pink-500 transition">
                            <input type="checkbox" name="has_episode" id="ept-episode" value="1" class="accent-pink-500">
                            <div><div class="text-sm font-bold text-pink-400">🎬 Episodios</div></div>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Orden</label>
                    <input type="number" name="order_index" id="ept-order" min="0" class="w-24 bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-purple-500 focus:outline-none">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-5">
                <button type="button" onclick="document.getElementById('modal-editar-pt').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-400 hover:text-white">Cancelar</button>
                <button type="submit" class="bg-purple-600 hover:bg-purple-500 px-5 py-2 rounded text-sm font-bold uppercase">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
// Nuevo ítem — preview
document.getElementById('ni-icon').addEventListener('input', function() { document.getElementById('ni-prev-icon').textContent = this.value || '❓'; });
document.getElementById('ni-label').addEventListener('input', function() { document.getElementById('ni-prev-label').textContent = this.value || 'NUEVO TIPO'; });
document.getElementById('ni-color').addEventListener('input', function() {
    document.getElementById('ni-color-hex').textContent = this.value;
    document.getElementById('ni-prev-label').style.color = this.value;
});

// Editar ítem
function abrirEditarItem(id, label, icon, colorHex, order) {
    document.getElementById('form-ei').action = '/admin/config/segment-types/' + id + '/update';
    document.getElementById('ei-label').value  = label;
    document.getElementById('ei-icon').value   = icon;
    document.getElementById('ei-color').value  = colorHex;
    document.getElementById('ei-order').value  = order;
    document.getElementById('ei-color-hex').textContent    = colorHex;
    document.getElementById('ei-prev-icon').textContent    = icon;
    document.getElementById('ei-prev-label').textContent   = label;
    document.getElementById('ei-prev-label').style.color   = colorHex;
    document.getElementById('modal-editar-item').classList.remove('hidden');
}
document.getElementById('ei-icon').addEventListener('input', function() { document.getElementById('ei-prev-icon').textContent = this.value; });
document.getElementById('ei-label').addEventListener('input', function() { document.getElementById('ei-prev-label').textContent = this.value; });
document.getElementById('ei-color').addEventListener('input', function() {
    document.getElementById('ei-color-hex').textContent = this.value;
    document.getElementById('ei-prev-label').style.color = this.value;
});

// Editar tipo de producción
function abrirEditarPT(id, label, icon, hasAirTime, hasLock, hasEpisode, order) {
    document.getElementById('form-ept').action       = '/admin/config/production-types/' + id + '/update';
    document.getElementById('ept-label').value       = label;
    document.getElementById('ept-icon').value        = icon;
    document.getElementById('ept-air').checked       = hasAirTime;
    document.getElementById('ept-lock').checked      = hasLock;
    document.getElementById('ept-episode').checked   = hasEpisode;
    document.getElementById('ept-order').value       = order;
    document.getElementById('modal-editar-pt').classList.remove('hidden');
}

// Escape
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        ['modal-nuevo-item','modal-editar-item','modal-nuevo-pt','modal-editar-pt']
            .forEach(id => document.getElementById(id)?.classList.add('hidden'));
    }
});
</script>
</body>
</html>
