<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $show->title }} — Escaletas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-900 text-white font-sans min-h-screen">

@include('partials.navbar')

<div class="max-w-5xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <header class="flex justify-between items-center mb-8 border-b border-gray-700 pb-6">
        <div class="flex items-center gap-4">
            <a href="/" class="text-gray-500 hover:text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-white">{{ $show->title }}</h1>
                    @if($show->channel)
                        <span class="text-xs text-gray-500 bg-gray-700 px-2 py-1 rounded">
                            📡 {{ $show->channel }}
                        </span>
                    @endif
                </div>
                @if($show->description)
                    <p class="text-gray-500 text-sm mt-1">{{ $show->description }}</p>
                @endif
            </div>
        </div>
        <div class="flex gap-2">
            @if(auth()->user()->isAdmin())
            <button onclick="document.getElementById('modal-editar-show').classList.remove('hidden')"
                class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded text-sm font-bold uppercase tracking-widest transition flex items-center gap-2 text-gray-300">
                ✏️ Editar Show
            </button>
            @endif
            @if(auth()->user()->isEditor())
            <button onclick="document.getElementById('modal-nueva-escaleta').classList.remove('hidden')"
                class="bg-blue-600 hover:bg-blue-500 px-5 py-2 rounded text-sm font-bold uppercase tracking-widest transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Escaleta
            </button>
            @endif
        </div>
    </header>

    {{-- STATS --}}
    @php
        $tz = 'America/Tegucigalpa';
        $ahora = \Carbon\Carbon::now($tz);
        $vencida = fn($r) => $ahora->greaterThan(
            \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $r->air_date.' '.($r->air_time??'00:00:00'), $tz)->addHour()
        );
        $emitidas   = $show->rundowns->filter($vencida)->count();
        $aprobadas  = $show->rundowns->where('status', 'aprobada')->reject($vencida)->count();
        $borradores = $show->rundowns->where('status', 'borrador')->reject($vencida)->count();
    @endphp
    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="bg-gray-800 rounded-lg p-4 border border-gray-700 text-center">
            <div class="text-2xl font-bold text-blue-400">{{ $show->rundowns->count() }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-widest mt-1">Total</div>
        </div>
        <div class="bg-gray-800 rounded-lg p-4 border border-gray-700 text-center">
            <div class="text-2xl font-bold text-gray-500">{{ $emitidas }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-widest mt-1">📼 Vencidas</div>
        </div>
        <div class="bg-gray-800 rounded-lg p-4 border border-green-900/50 text-center">
            <div class="text-2xl font-bold text-green-400">{{ $aprobadas }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-widest mt-1">✅ Aprobadas</div>
        </div>
        <div class="bg-gray-800 rounded-lg p-4 border border-gray-700 text-center">
            <div class="text-2xl font-bold text-yellow-400">{{ $borradores }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-widest mt-1">✏️ Borradores</div>
        </div>
    </div>

    {{-- LISTA DE ESCALETAS --}}
    @if($show->rundowns->count() > 0)
    @php
        $rundownsOrdenadas = $show->rundowns->sortByDesc('air_date');
    @endphp
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
            <div class="px-5 py-3 bg-gray-700/50 border-b border-gray-700">
                <h2 class="text-xs font-bold uppercase text-gray-400 tracking-widest">Escaletas</h2>
            </div>
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700 text-xs uppercase text-gray-500 tracking-widest">
                        <th class="px-5 py-3 text-left">Fecha</th>
                        <th class="px-5 py-3 text-left">Hora</th>
                        <th class="px-5 py-3 text-left">Estado</th>
                        <th class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @foreach($rundownsOrdenadas as $rundown)
                    @php
                        $airDT   = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $rundown->air_date . ' ' . ($rundown->air_time ?? '00:00:00'), 'America/Tegucigalpa');
                        $emitida = \Carbon\Carbon::now('America/Tegucigalpa')->greaterThan($airDT->copy()->addHour());
                        $aprobada = !$emitida && $rundown->status === 'aprobada';
                        $borrador = !$emitida && $rundown->status !== 'aprobada';
                    @endphp
                    <tr class="hover:bg-gray-700/30 transition group
                        {{ $emitida ? 'opacity-50' : '' }}">

                        {{-- Fecha --}}
                        <td class="px-5 py-4">
                            <div class="font-bold {{ $emitida ? 'text-gray-500' : 'text-white' }}">
                                {{ \Carbon\Carbon::parse($rundown->air_date)->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($rundown->air_date)->translatedFormat('l') }}
                            </div>
                        </td>

                        {{-- Hora --}}
                        <td class="px-5 py-4">
                            <span class="font-mono {{ $emitida ? 'text-gray-600' : 'text-yellow-400' }} text-sm">
                                {{ substr($rundown->air_time ?? '00:00:00', 0, 5) }}
                            </span>
                        </td>

                        {{-- Estado --}}
                        <td class="px-5 py-4">
                            @if($emitida)
                                <span class="text-[10px] font-bold uppercase px-2 py-1 rounded bg-gray-700/50 text-gray-500">
                                    📼 Vencida
                                </span>
                            @elseif($aprobada)
                                <span class="text-[10px] font-bold uppercase px-2 py-1 rounded bg-green-400/10 text-green-400">
                                    ✅ Aprobada
                                </span>
                            @else
                                <span class="text-[10px] font-bold uppercase px-2 py-1 rounded bg-yellow-400/10 text-yellow-400">
                                    ✏️ Borrador
                                </span>
                            @endif
                        </td>

                        {{-- Acciones --}}
                        <td class="px-5 py-4 text-right">
                            <div class="flex justify-end gap-2 items-center">

                                {{-- Abrir --}}
                                <a href="/rundown/{{ $rundown->id }}"
                                    class="px-3 py-1 rounded text-xs font-bold uppercase transition
                                    {{ $emitida
                                        ? 'bg-gray-700 hover:bg-gray-600 text-gray-400'
                                        : 'bg-blue-600 hover:bg-blue-500 text-white' }}">
                                    {{ $emitida ? '👁 Ver' : '✏️ Abrir' }}
                                </a>

                                @if(!$emitida)
                                    {{-- Aprobar / Regresar a borrador --}}
                                    @if($borrador)
                                        <button onclick="fetchAccion('/rundown/{{ $rundown->id }}/aprobar')"
                                            class="bg-green-800 hover:bg-green-700 px-3 py-1 rounded text-xs font-bold uppercase transition text-green-300">
                                            ✅ Aprobar
                                        </button>
                                    @else
                                        <button onclick="fetchAccion('/rundown/{{ $rundown->id }}/desaprobar')"
                                            class="bg-yellow-900/50 hover:bg-yellow-800 px-3 py-1 rounded text-xs font-bold uppercase transition text-yellow-400">
                                            ↩ Borrador
                                        </button>
                                    @endif

                                    {{-- Editar fecha/hora --}}
                                    <button onclick="abrirEditar({{ $rundown->id }}, '{{ $rundown->air_date }}', '{{ substr($rundown->air_time ?? '19:00:00', 0, 5) }}')"
                                        class="bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded text-xs font-bold uppercase transition text-gray-300">
                                        🕐
                                    </button>

                                    {{-- Duplicar --}}
                                    <button onclick="abrirDuplicar({{ $rundown->id }}, '{{ $rundown->air_date }}')"
                                        class="bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded text-xs font-bold uppercase transition text-gray-300">
                                        📋
                                    </button>

                                    {{-- Eliminar --}}
                                    <button onclick="fetchEliminar('/rundown/{{ $rundown->id }}/delete', '¿Eliminar esta escaleta?')"
                                        class="bg-red-900/40 hover:bg-red-700 px-3 py-1 rounded text-xs font-bold uppercase transition text-red-400">
                                        🗑
                                    </button>
                                @else
                                    {{-- VENCIDA: duplicar siempre; editar/eliminar solo admin --}}
                                    <button onclick="abrirDuplicar({{ $rundown->id }}, '{{ $rundown->air_date }}')"
                                        class="bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded text-xs font-bold uppercase transition text-gray-400">
                                        📋 Duplicar
                                    </button>
                                    @if(auth()->user()->isAdmin())
                                        <button onclick="abrirEditar({{ $rundown->id }}, '{{ $rundown->air_date }}', '{{ substr($rundown->air_time ?? '19:00:00', 0, 5) }}')"
                                            class="bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded text-xs font-bold uppercase transition text-gray-300"
                                            title="Cambiar fecha/hora para desbloquear">
                                            🕐
                                        </button>
                                        <button onclick="fetchEliminar('/rundown/{{ $rundown->id }}/delete', '¿Eliminar esta escaleta vencida? No se puede deshacer.')"
                                            class="bg-red-900/40 hover:bg-red-700 px-3 py-1 rounded text-xs font-bold uppercase transition text-red-400">
                                            🗑
                                        </button>
                                    @endif
                                @endif

                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-20 text-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-lg italic">No hay escaletas aún. ¡Crea la primera!</p>
        </div>
    @endif

</div>

{{-- MODAL EDITAR SHOW --}}
<div id="modal-editar-show" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
    <div class="bg-gray-800 rounded-xl border border-gray-700 w-full max-w-md p-6 shadow-2xl">
        <h2 class="text-lg font-bold text-white mb-5">Editar Show</h2>
        <form method="POST" action="/shows/{{ $show->id }}/update">
            @csrf
            <div class="flex flex-col gap-4">
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Título *</label>
                    <input type="text" name="title" required value="{{ $show->title }}"
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Canal</label>
                    <input type="text" name="channel" value="{{ $show->channel }}"
                        placeholder="Ej: Canal 5, Televicentro..."
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Descripción</label>
                    <textarea name="description" rows="2"
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none resize-none">{{ $show->description }}</textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('modal-editar-show').classList.add('hidden')"
                    class="px-4 py-2 text-sm text-gray-400 hover:text-white transition">Cancelar</button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-500 px-5 py-2 rounded text-sm font-bold uppercase transition">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL NUEVA ESCALETA --}}
<div id="modal-nueva-escaleta" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
    <div class="bg-gray-800 rounded-xl border border-gray-700 w-full max-w-md p-6 shadow-2xl">
        <h2 class="text-lg font-bold text-white mb-1">Nueva Escaleta</h2>
        <p class="text-gray-500 text-sm mb-5">{{ $show->title }}</p>
        <form method="POST" action="/shows/{{ $show->id }}/rundowns">
            @csrf
            <div class="flex flex-col gap-4">
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Fecha de Emisión *</label>
                    <input type="date" name="air_date" required value="{{ date('Y-m-d') }}"
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Hora de Inicio *</label>
                    <input type="time" name="air_time" required value="19:00"
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('modal-nueva-escaleta').classList.add('hidden')"
                    class="px-4 py-2 text-sm text-gray-400 hover:text-white transition">Cancelar</button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-500 px-5 py-2 rounded text-sm font-bold uppercase transition">
                    Crear Escaleta
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDITAR FECHA/HORA --}}
<div id="modal-editar-escaleta" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
    <div class="bg-gray-800 rounded-xl border border-gray-700 w-full max-w-md p-6 shadow-2xl">
        <h2 class="text-lg font-bold text-white mb-1">Editar Fecha y Hora</h2>
        <p class="text-gray-500 text-sm mb-5">Cambia la fecha u hora de emisión de esta escaleta.</p>
        <form method="POST" id="form-editar-escaleta" action="">
            @csrf
            <div class="flex flex-col gap-4">
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Fecha de Emisión *</label>
                    <input type="date" name="air_date" id="edit-air-date" required
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Hora de Inicio *</label>
                    <input type="time" name="air_time" id="edit-air-time" required
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('modal-editar-escaleta').classList.add('hidden')"
                    class="px-4 py-2 text-sm text-gray-400 hover:text-white transition">Cancelar</button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-500 px-5 py-2 rounded text-sm font-bold uppercase transition">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL DUPLICAR --}}
<div id="modal-duplicar" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
    <div class="bg-gray-800 rounded-xl border border-gray-700 w-full max-w-md p-6 shadow-2xl">
        <h2 class="text-lg font-bold text-white mb-1">Duplicar Escaleta</h2>
        <p class="text-gray-500 text-sm mb-5">Se copiarán todos los bloques e ítems. El guion literario no se copia.</p>
        <form method="POST" id="form-duplicar" action="">
            @csrf
            <div class="flex flex-col gap-4">
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Nueva Fecha de Emisión *</label>
                    <input type="date" name="air_date" id="dup-air-date" required value="{{ date('Y-m-d') }}"
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Hora de Inicio *</label>
                    <input type="time" name="air_time" id="dup-air-time" required value="19:00"
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('modal-duplicar').classList.add('hidden')"
                    class="px-4 py-2 text-sm text-gray-400 hover:text-white transition">Cancelar</button>
                <button type="submit"
                    class="bg-purple-600 hover:bg-purple-500 px-5 py-2 rounded text-sm font-bold uppercase transition">
                    📋 Duplicar y Abrir
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirEditar(rundownId, airDate, airTime) {
        document.getElementById('form-editar-escaleta').action = '/rundown/' + rundownId + '/update-datetime';
        document.getElementById('edit-air-date').value = airDate;
        document.getElementById('edit-air-time').value = airTime;
        document.getElementById('modal-editar-escaleta').classList.remove('hidden');
    }

    function abrirDuplicar(rundownId, airDate) {
        document.getElementById('form-duplicar').action = '/rundown/' + rundownId + '/duplicate';
        const fecha = new Date(airDate);
        fecha.setDate(fecha.getDate() + 7);
        document.getElementById('dup-air-date').value = fecha.toISOString().split('T')[0];
        document.getElementById('modal-duplicar').classList.remove('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.getElementById('modal-nueva-escaleta').classList.add('hidden');
            document.getElementById('modal-duplicar').classList.add('hidden');
            document.getElementById('modal-editar-escaleta').classList.add('hidden');
            document.getElementById('modal-editar-show').classList.add('hidden');
        }
    });

    // ── Acciones via fetch (evita navegación completa en 403) ─────────────
    const _csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    async function fetchAccion(url, method = 'POST') {
        const res = await fetch(url, {
            method,
            headers: {
                'X-CSRF-TOKEN': _csrf,
                'Accept': 'application/json',
            }
        });
        if (res.ok) {
            window.location.reload();
        }
        // Los 403 los captura el wrapper de fetch en navbar.blade.php
    }

    async function fetchEliminar(url, confirmMsg) {
        if (!confirm(confirmMsg)) return;
        const res = await fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': _csrf,
                'Accept': 'application/json',
            }
        });
        if (res.ok) {
            window.location.reload();
        }
        // Los 403 los captura el wrapper de fetch en navbar.blade.php
    }
</script>

</body>
</html>
