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
        <button onclick="document.getElementById('modal-nueva-escaleta').classList.remove('hidden')"
            class="bg-blue-600 hover:bg-blue-500 px-5 py-2 rounded text-sm font-bold uppercase tracking-widest transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Escaleta
        </button>
    </header>

    {{-- STATS --}}
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-gray-800 rounded-lg p-4 border border-gray-700 text-center">
            <div class="text-2xl font-bold text-blue-400">{{ $show->rundowns->count() }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-widest mt-1">Total Escaletas</div>
        </div>
        <div class="bg-gray-800 rounded-lg p-4 border border-gray-700 text-center">
            <div class="text-2xl font-bold text-green-400">
                {{ $show->rundowns->where('status', 'produccion')->count() }}
            </div>
            <div class="text-xs text-gray-500 uppercase tracking-widest mt-1">En Producción</div>
        </div>
        <div class="bg-gray-800 rounded-lg p-4 border border-gray-700 text-center">
            <div class="text-2xl font-bold text-gray-400">
                {{ $show->rundowns->where('status', 'borrador')->count() }}
            </div>
            <div class="text-xs text-gray-500 uppercase tracking-widest mt-1">Borradores</div>
        </div>
    </div>

    {{-- LISTA DE ESCALETAS --}}
    @if($show->rundowns->count() > 0)
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
                    @foreach($show->rundowns as $rundown)
                    <tr class="hover:bg-gray-700/30 transition group">
                        <td class="px-5 py-4">
                            <div class="font-bold text-white">
                                {{ \Carbon\Carbon::parse($rundown->air_date)->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($rundown->air_date)->translatedFormat('l') }}
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-mono text-yellow-400 text-sm">
                                {{ substr($rundown->air_time ?? '00:00:00', 0, 5) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @php
                                $statusStyles = [
                                    'produccion' => 'bg-green-400/10 text-green-400',
                                    'borrador'   => 'bg-gray-600/30 text-gray-400',
                                    'archivado'  => 'bg-red-400/10 text-red-400',
                                ];
                                $style = $statusStyles[$rundown->status] ?? 'bg-gray-600/30 text-gray-400';
                            @endphp
                            <span class="text-[10px] font-bold uppercase px-2 py-1 rounded {{ $style }}">
                                {{ ucfirst($rundown->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                {{-- Abrir escaleta --}}
                                <a href="/rundown/{{ $rundown->id }}"
                                    class="bg-blue-600 hover:bg-blue-500 px-3 py-1 rounded text-xs font-bold uppercase transition">
                                    ✏️ Abrir
                                </a>

                                {{-- Duplicar --}}
                                <button onclick="abrirDuplicar({{ $rundown->id }}, '{{ $rundown->air_date }}')"
                                    class="bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded text-xs font-bold uppercase transition text-gray-300">
                                    📋 Duplicar
                                </button>

                                {{-- Eliminar --}}
                                <form method="POST" action="/rundown/{{ $rundown->id }}/delete"
                                    onsubmit="return confirm('¿Eliminar esta escaleta? Esta acción no se puede deshacer.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-900/40 hover:bg-red-700 px-3 py-1 rounded text-xs font-bold uppercase transition text-red-400">
                                        🗑
                                    </button>
                                </form>
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
                    <input type="date" name="air_date" required
                        value="{{ date('Y-m-d') }}"
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Hora de Inicio *</label>
                    <input type="time" name="air_time" required
                        value="19:00"
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('modal-nueva-escaleta').classList.add('hidden')"
                    class="px-4 py-2 text-sm text-gray-400 hover:text-white transition">
                    Cancelar
                </button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-500 px-5 py-2 rounded text-sm font-bold uppercase transition">
                    Crear Escaleta
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL DUPLICAR ESCALETA --}}
<div id="modal-duplicar" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
    <div class="bg-gray-800 rounded-xl border border-gray-700 w-full max-w-md p-6 shadow-2xl">
        <h2 class="text-lg font-bold text-white mb-1">Duplicar Escaleta</h2>
        <p class="text-gray-500 text-sm mb-5">Se copiarán todos los bloques e ítems. El guion literario no se copia.</p>
        <form method="POST" id="form-duplicar" action="">
            @csrf
            <div class="flex flex-col gap-4">
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Nueva Fecha de Emisión *</label>
                    <input type="date" name="air_date" id="dup-air-date" required
                        value="{{ date('Y-m-d') }}"
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Hora de Inicio *</label>
                    <input type="time" name="air_time" id="dup-air-time" required
                        value="19:00"
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('modal-duplicar').classList.add('hidden')"
                    class="px-4 py-2 text-sm text-gray-400 hover:text-white transition">
                    Cancelar
                </button>
                <button type="submit"
                    class="bg-purple-600 hover:bg-purple-500 px-5 py-2 rounded text-sm font-bold uppercase transition">
                    📋 Duplicar y Abrir
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirDuplicar(rundownId, airDate) {
        document.getElementById('form-duplicar').action = '/rundown/' + rundownId + '/duplicate';
        // Sugerir fecha del día siguiente
        const fecha = new Date(airDate);
        fecha.setDate(fecha.getDate() + 7);
        document.getElementById('dup-air-date').value = fecha.toISOString().split('T')[0];
        document.getElementById('modal-duplicar').classList.remove('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.getElementById('modal-nueva-escaleta').classList.add('hidden');
            document.getElementById('modal-duplicar').classList.add('hidden');
        }
    });
</script>

</body>
</html>
