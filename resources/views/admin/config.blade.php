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

<div class="max-w-5xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <header class="flex items-center justify-between mb-10 border-b border-gray-700 pb-6">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-wide">⚙️ Configuración Global</h1>
            <p class="text-gray-500 text-sm mt-1">Administra los tipos de ítem por tipo de producción</p>
        </div>
        <a href="/" class="text-gray-500 hover:text-white text-sm transition">← Volver al catálogo</a>
    </header>

    {{-- FLASH --}}
    @if(session('success'))
        <div class="mb-6 bg-green-900/30 border border-green-700 text-green-400 text-sm px-4 py-3 rounded flex items-center justify-between">
            <span>✓ {{ session('success') }}</span>
        </div>
    @endif

    {{-- SECCIÓN: TIPOS DE ÍTEM --}}
    <section>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-white">Tipos de Ítem</h2>
                <p class="text-xs text-gray-500 mt-0.5">Define qué tipos aparecen en el selector según el tipo de producción</p>
            </div>
            <button onclick="abrirModalNuevo()"
                class="bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded text-sm font-bold uppercase tracking-widest transition flex items-center gap-2">
                + Nuevo Tipo
            </button>
        </div>

        {{-- Tabs por tipo de producción --}}
        <div x-data="{ tab: '{{ $productionTypes[0]['value'] ?? 'live' }}' }" x-cloak>

            {{-- Tab headers --}}
            <div class="flex gap-1 mb-6 border-b border-gray-700">
                @foreach($productionTypes as $pt)
                <button
                    onclick="switchTab('{{ $pt['value'] }}')"
                    id="tab-btn-{{ $pt['value'] }}"
                    class="tab-btn px-5 py-2.5 text-sm font-bold uppercase tracking-widest transition border-b-2 -mb-px
                        {{ $loop->first
                            ? 'border-blue-500 text-blue-400'
                            : 'border-transparent text-gray-500 hover:text-gray-300' }}">
                    {{ $pt['icon'] }} {{ $pt['label'] }}
                </button>
                @endforeach
            </div>

            {{-- Tab panels --}}
            @foreach($productionTypes as $pt)
            <div id="tab-panel-{{ $pt['value'] }}"
                class="tab-panel {{ $loop->first ? '' : 'hidden' }}">

                <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-700 text-xs uppercase text-gray-500 tracking-widest">
                                <th class="px-4 py-3 text-left w-10">Orden</th>
                                <th class="px-4 py-3 text-left">Ícono</th>
                                <th class="px-4 py-3 text-left">Label</th>
                                <th class="px-4 py-3 text-left">Value (clave)</th>
                                <th class="px-4 py-3 text-left">Color</th>
                                <th class="px-4 py-3 text-left">Estado</th>
                                <th class="px-4 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/50">
                            @forelse($tiposPorProduccion[$pt['value']] as $tipo)
                            <tr class="hover:bg-gray-700/20 transition {{ $tipo->active ? '' : 'opacity-40' }}">
                                <td class="px-4 py-3 text-center">
                                    <span class="text-xs font-mono text-gray-500">{{ $tipo->order_index }}</span>
                                </td>
                                <td class="px-4 py-3 text-xl">{{ $tipo->icon }}</td>
                                <td class="px-4 py-3">
                                    <span class="font-bold text-sm" style="color: {{ $tipo->color_hex }}">
                                        {{ $tipo->label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-mono text-xs bg-gray-900 px-2 py-1 rounded text-gray-400">
                                        {{ $tipo->value }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 rounded-full border border-gray-600"
                                             style="background-color: {{ $tipo->color_hex }}"></div>
                                        <span class="text-xs font-mono text-gray-500">{{ $tipo->color_hex }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="/admin/config/segment-types/{{ $tipo->id }}/toggle" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="text-xs font-bold uppercase px-2 py-1 rounded transition
                                                {{ $tipo->active
                                                    ? 'bg-green-900/30 text-green-400 hover:bg-red-900/30 hover:text-red-400'
                                                    : 'bg-gray-700 text-gray-500 hover:bg-green-900/30 hover:text-green-400' }}">
                                            {{ $tipo->active ? '✓ Activo' : '✗ Inactivo' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            onclick="abrirModalEditar({{ $tipo->id }}, '{{ addslashes($tipo->label) }}', '{{ $tipo->icon }}', '{{ $tipo->color_hex }}', {{ $tipo->order_index }})"
                                            class="text-xs text-gray-500 hover:text-white transition px-2 py-1 rounded hover:bg-gray-700">
                                            ✏️ Editar
                                        </button>
                                        <form method="POST" action="/admin/config/segment-types/{{ $tipo->id }}/delete"
                                            onsubmit="return confirm('¿Eliminar el tipo {{ $tipo->label }}? Los ítems existentes con este tipo no se borran.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-xs text-gray-600 hover:text-red-400 transition px-2 py-1 rounded hover:bg-gray-700">
                                                🗑
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-600 text-sm italic">
                                    No hay tipos definidos para este tipo de producción.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
            @endforeach

        </div>
    </section>

</div>

{{-- MODAL NUEVO TIPO --}}
<div id="modal-nuevo-tipo" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50"
     onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-gray-800 rounded-xl border border-gray-700 w-full max-w-md p-6 shadow-2xl">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-white">➕ Nuevo Tipo de Ítem</h2>
            <button onclick="document.getElementById('modal-nuevo-tipo').classList.add('hidden')"
                class="text-gray-600 hover:text-white transition">✕</button>
        </div>
        <form method="POST" action="/admin/config/segment-types">
            @csrf
            <div class="flex flex-col gap-4">

                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Tipo de Producción *</label>
                    <select name="production_type" id="nuevo-production-type"
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none text-sm">
                        @foreach($productionTypes as $pt)
                            <option value="{{ $pt['value'] }}">{{ $pt['icon'] }} {{ $pt['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Ícono (emoji) *</label>
                        <input type="text" name="icon" id="nuevo-icon"
                            placeholder="🎬" maxlength="5"
                            class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none text-2xl text-center">
                    </div>
                    <div>
                        <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Color *</label>
                        <div class="flex gap-2 items-center">
                            <input type="color" name="color_hex" id="nuevo-color"
                                value="#3b82f6"
                                class="w-10 h-10 rounded border border-gray-600 bg-gray-900 cursor-pointer p-0.5">
                            <span id="nuevo-color-hex" class="text-xs font-mono text-gray-400">#3b82f6</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Label (nombre visible) *</label>
                    <input type="text" name="label" placeholder="Ej: ENTREVISTA"
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none uppercase">
                </div>

                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">
                        Value (clave interna) *
                        <span class="text-gray-600 normal-case font-normal">— sin espacios, solo letras y _</span>
                    </label>
                    <input type="text" name="value" placeholder="Ej: ENTREVISTA"
                        pattern="[A-Z_]+" title="Solo mayúsculas y guión bajo"
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none font-mono uppercase">
                </div>

                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Orden</label>
                    <input type="number" name="order_index" value="99" min="0"
                        class="w-24 bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>

                {{-- Preview --}}
                <div class="bg-gray-900/50 border border-gray-700 rounded p-3 flex items-center gap-3">
                    <span id="preview-icon" class="text-2xl">🎬</span>
                    <span id="preview-label" class="font-bold text-sm" style="color: #3b82f6">NUEVO TIPO</span>
                </div>

            </div>
            <div class="flex justify-end gap-3 mt-5">
                <button type="button" onclick="document.getElementById('modal-nuevo-tipo').classList.add('hidden')"
                    class="px-4 py-2 text-sm text-gray-400 hover:text-white transition">Cancelar</button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-500 px-5 py-2 rounded text-sm font-bold uppercase transition">
                    Crear Tipo
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDITAR TIPO --}}
<div id="modal-editar-tipo" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50"
     onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-gray-800 rounded-xl border border-gray-700 w-full max-w-md p-6 shadow-2xl">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-white">✏️ Editar Tipo de Ítem</h2>
            <button onclick="document.getElementById('modal-editar-tipo').classList.add('hidden')"
                class="text-gray-600 hover:text-white transition">✕</button>
        </div>
        <form method="POST" id="form-editar-tipo" action="">
            @csrf
            <div class="flex flex-col gap-4">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Ícono (emoji) *</label>
                        <input type="text" name="icon" id="editar-icon"
                            maxlength="5"
                            class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none text-2xl text-center">
                    </div>
                    <div>
                        <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Color *</label>
                        <div class="flex gap-2 items-center">
                            <input type="color" name="color_hex" id="editar-color"
                                class="w-10 h-10 rounded border border-gray-600 bg-gray-900 cursor-pointer p-0.5">
                            <span id="editar-color-hex" class="text-xs font-mono text-gray-400"></span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Label (nombre visible) *</label>
                    <input type="text" name="label" id="editar-label"
                        class="w-full bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none uppercase">
                </div>

                <div>
                    <label class="text-xs uppercase text-gray-400 font-bold tracking-widest block mb-1">Orden</label>
                    <input type="number" name="order_index" id="editar-order" min="0"
                        class="w-24 bg-gray-900 border border-gray-600 rounded px-3 py-2 text-white focus:border-blue-500 focus:outline-none">
                </div>

                {{-- Preview --}}
                <div class="bg-gray-900/50 border border-gray-700 rounded p-3 flex items-center gap-3">
                    <span id="editar-preview-icon" class="text-2xl"></span>
                    <span id="editar-preview-label" class="font-bold text-sm"></span>
                </div>

            </div>
            <div class="flex justify-end gap-3 mt-5">
                <button type="button" onclick="document.getElementById('modal-editar-tipo').classList.add('hidden')"
                    class="px-4 py-2 text-sm text-gray-400 hover:text-white transition">Cancelar</button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-500 px-5 py-2 rounded text-sm font-bold uppercase transition">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ── Tabs ──────────────────────────────────────────────────────────────────
function switchTab(value) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('border-blue-500', 'text-blue-400');
        b.classList.add('border-transparent', 'text-gray-500');
    });
    document.getElementById('tab-panel-' + value).classList.remove('hidden');
    const btn = document.getElementById('tab-btn-' + value);
    btn.classList.add('border-blue-500', 'text-blue-400');
    btn.classList.remove('border-transparent', 'text-gray-500');
}

// ── Modal Nuevo ───────────────────────────────────────────────────────────
function abrirModalNuevo() {
    document.getElementById('modal-nuevo-tipo').classList.remove('hidden');
}

// Preview en tiempo real
document.getElementById('nuevo-icon').addEventListener('input', function() {
    document.getElementById('preview-icon').textContent = this.value || '❓';
});
document.querySelector('input[name="label"]')?.addEventListener('input', function() {
    document.getElementById('preview-label').textContent = this.value || 'NUEVO TIPO';
});
document.getElementById('nuevo-color').addEventListener('input', function() {
    document.getElementById('nuevo-color-hex').textContent = this.value;
    document.getElementById('preview-label').style.color = this.value;
});

// ── Modal Editar ──────────────────────────────────────────────────────────
function abrirModalEditar(id, label, icon, colorHex, order) {
    document.getElementById('form-editar-tipo').action = '/admin/config/segment-types/' + id + '/update';
    document.getElementById('editar-label').value    = label;
    document.getElementById('editar-icon').value     = icon;
    document.getElementById('editar-color').value    = colorHex;
    document.getElementById('editar-order').value    = order;
    document.getElementById('editar-color-hex').textContent      = colorHex;
    document.getElementById('editar-preview-icon').textContent   = icon;
    document.getElementById('editar-preview-label').textContent  = label;
    document.getElementById('editar-preview-label').style.color  = colorHex;
    document.getElementById('modal-editar-tipo').classList.remove('hidden');
}

document.getElementById('editar-icon').addEventListener('input', function() {
    document.getElementById('editar-preview-icon').textContent = this.value;
});
document.getElementById('editar-label').addEventListener('input', function() {
    document.getElementById('editar-preview-label').textContent = this.value;
});
document.getElementById('editar-color').addEventListener('input', function() {
    document.getElementById('editar-color-hex').textContent = this.value;
    document.getElementById('editar-preview-label').style.color = this.value;
});

// Cerrar con Escape
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.getElementById('modal-nuevo-tipo').classList.add('hidden');
        document.getElementById('modal-editar-tipo').classList.add('hidden');
    }
});
</script>

</body>
</html>
