<div class="flex flex-col gap-0" id="properties-panel">

@php $locked = $locked ?? false; @endphp

    {{-- HEADER --}}
    <div class="flex items-center justify-between border-b border-gray-700 pb-3 mb-4">
        <div class="flex items-center gap-2">
            <span class="font-mono text-xs font-bold text-blue-400 bg-blue-900/40 px-2 py-1 rounded">
                {{ $segNum ?? '—' }}
            </span>
            <span class="text-[10px] uppercase text-gray-500 tracking-widest">
                {{ $locked ? 'Solo Lectura' : 'Propiedades' }}
            </span>
            @if($locked)
                <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded bg-gray-700/50 text-gray-500">📼 Vencida</span>
            @endif
        </div>
        <button onclick="deseleccionarSegmento()"
            class="text-gray-600 hover:text-white transition text-sm leading-none">✕</button>
    </div>

    {{-- TÍTULO --}}
    <div class="mb-4">
        <label class="text-[10px] uppercase text-gray-500 font-bold tracking-widest block mb-1">Título</label>
        @if($locked)
            <div class="w-full bg-gray-900/50 border border-gray-700/50 rounded px-3 py-2 text-gray-400 text-sm">
                {{ $segment->title }}
            </div>
        @else
        <input
            type="text"
            name="title"
            value="{{ $segment->title }}"
            hx-post="/segment/{{ $segment->id }}/update-field"
            hx-trigger="keyup[key=='Enter'], blur"
            hx-target="#tabla-segmentos"
            hx-swap="innerHTML"
            onkeydown="if(event.key==='Enter') this.blur()"
            class="w-full bg-gray-900 border border-gray-700 rounded px-3 py-2 text-white text-sm
                   focus:border-blue-500 focus:outline-none hover:border-gray-500 transition">
        @endif
    </div>

    {{-- TOGGLES: Guion + Teleprompter --}}
    <div class="flex gap-4 mb-3">

        {{-- Guion literario --}}
        @if($locked)
            <div class="flex items-center gap-2 flex-1 bg-gray-900/30 border border-gray-700/50 rounded px-3 py-2 opacity-50">
                <input type="checkbox" {{ $segment->has_script ? 'checked' : '' }} disabled class="w-3.5 h-3.5 rounded">
                <div>
                    <div class="text-[10px] font-bold uppercase text-gray-600 tracking-widest">Guion</div>
                    <div class="text-[9px] text-gray-700">Literario</div>
                </div>
            </div>
        @else
        <label class="flex items-center gap-2 cursor-pointer group flex-1 bg-gray-900/50 border border-gray-700 rounded px-3 py-2 hover:border-blue-600 transition">
            <input
                type="checkbox"
                {{ $segment->has_script ? 'checked' : '' }}
                hx-post="/segment/{{ $segment->id }}/toggle-script"
                hx-target="#editor-container"
                hx-swap="innerHTML"
                class="w-3.5 h-3.5 rounded accent-blue-500 cursor-pointer">
            <div>
                <div class="text-[10px] font-bold uppercase text-gray-400 group-hover:text-blue-400 transition tracking-widest">Guion</div>
                <div class="text-[9px] text-gray-600">Literario</div>
            </div>
        </label>
        @endif

        {{-- Teleprompter --}}
        @if($locked)
            <div class="flex items-center gap-2 flex-1 bg-gray-900/30 border border-gray-700/50 rounded px-3 py-2 opacity-50">
                <input type="checkbox" {{ $segment->in_prompter ? 'checked' : '' }} disabled class="w-3.5 h-3.5 rounded">
                <div>
                    <div class="text-[10px] font-bold uppercase text-gray-600 tracking-widest">Prompter</div>
                    <div class="text-[9px] text-gray-700">Va al aire</div>
                </div>
            </div>
        @else
        <label class="flex items-center gap-2 cursor-pointer group flex-1 bg-gray-900/50 border border-gray-700 rounded px-3 py-2 hover:border-yellow-600 transition">
            <input
                type="checkbox"
                {{ $segment->in_prompter ? 'checked' : '' }}
                hx-post="/segment/{{ $segment->id }}/toggle-prompter"
                hx-target="#editor-container"
                hx-swap="innerHTML"
                class="w-3.5 h-3.5 rounded accent-yellow-500 cursor-pointer">
            <div>
                <div class="text-[10px] font-bold uppercase text-gray-400 group-hover:text-yellow-400 transition tracking-widest">Prompter</div>
                <div class="text-[9px] text-gray-600">Va al aire</div>
            </div>
        </label>
        @endif

    </div>

    {{-- GUION LITERARIO --}}
    @if($segment->has_script)
        <div class="mb-5">
            @if($locked)
                <div class="w-full bg-gray-900/50 border border-gray-700/50 rounded p-3 text-gray-400 font-mono text-sm leading-relaxed whitespace-pre-wrap min-h-[10rem]">{{ $segment->script_content ?: '(Sin contenido)' }}</div>
            @else
            <div id="save-indicator" class="text-[10px] text-gray-600 italic mb-1 text-right h-3"></div>
            <textarea
                name="script_content"
                hx-post="/segment/{{ $segment->id }}/update-script"
                hx-trigger="keyup changed delay:800ms"
                hx-target="#save-indicator"
                placeholder="Escribe el guion literario aquí..."
                class="w-full bg-gray-900 text-gray-100 p-3 rounded border border-gray-700
                       focus:border-blue-500 outline-none resize-none font-mono text-sm leading-relaxed
                       hover:border-gray-500 transition"
                rows="10"
            >{{ $segment->script_content }}</textarea>
            @endif
        </div>
    @else
        <div class="bg-gray-900/50 border border-dashed border-gray-700 rounded p-3 text-center mb-5">
            <p class="text-xs text-gray-600 italic">Sin guion literario activado.</p>
        </div>
    @endif

    {{-- NOTAS DE PRODUCCIÓN — botón modal --}}
    <div class="mb-4">
        @if($locked)
            @if($segment->production_notes)
                <div class="w-full bg-gray-900/50 border border-gray-700/50 rounded p-3 text-gray-400 text-xs leading-relaxed whitespace-pre-wrap">
                    <div class="text-[10px] uppercase text-gray-600 font-bold tracking-widest mb-1">📋 Notas de Producción</div>
                    {{ $segment->production_notes }}
                </div>
            @endif
        @else
            <button
                onclick="abrirModalNotas({{ $segment->id }}, {{ json_encode($segment->production_notes) }})"
                class="w-full flex items-center justify-between px-3 py-2 rounded border transition text-xs font-bold uppercase tracking-widest
                    {{ $segment->production_notes
                        ? 'bg-amber-900/20 border-amber-700/50 text-amber-400 hover:bg-amber-900/40 hover:border-amber-600'
                        : 'bg-gray-900/50 border-gray-700 border-dashed text-gray-600 hover:text-gray-400 hover:border-gray-500' }}">
                <span>
                    {{ $segment->production_notes ? '📋 Editar nota de producción' : '➕ Agregar nota de producción' }}
                </span>
                @if($segment->production_notes)
                    <span class="text-[9px] font-normal normal-case text-amber-600 truncate max-w-[120px] ml-2">
                        {{ Str::limit($segment->production_notes, 30) }}
                    </span>
                @endif
            </button>
        @endif
    </div>

    {{-- SEPARADOR --}}
    <div class="border-t border-gray-700/60 mb-4"></div>

    {{-- TIPO + DURACIÓN --}}
    @php
        $productionType = $segment->block->rundown->show->production_type ?? 'live';
        $segmentTypes   = \App\Config\SegmentTypes::forType($productionType);
        $currentType    = collect($segmentTypes)->firstWhere('value', $segment->type);
        $tipoColor      = $currentType['color'] ?? 'text-gray-400';
        $tipoLabel      = $currentType['label'] ?? $segment->type;
    @endphp
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="text-[10px] uppercase text-gray-500 font-bold tracking-widest block mb-1">Tipo</label>
            @if($locked)
                <div class="w-full bg-gray-900/50 border border-gray-700/50 rounded px-2 py-2 text-xs {{ $tipoColor }}">
                    {{ $tipoLabel }}
                </div>
            @else
            <select
                name="type"
                hx-post="/segment/{{ $segment->id }}/update-field"
                hx-trigger="change"
                hx-target="#tabla-segmentos"
                hx-swap="innerHTML"
                class="w-full bg-gray-900 border border-gray-700 rounded px-2 py-2 text-xs
                       focus:border-blue-500 focus:outline-none cursor-pointer {{ $tipoColor }}">
                @foreach($segmentTypes as $st)
                    <option value="{{ $st['value'] }}"
                        {{ $segment->type === $st['value'] ? 'selected' : '' }}
                        class="bg-gray-800 {{ $st['color'] }}">
                        {{ $st['label'] }}
                    </option>
                @endforeach
            </select>
            @endif
        </div>

        <div>
            <label class="text-[10px] uppercase text-gray-500 font-bold tracking-widest block mb-1">
                Duración <span class="text-gray-700 normal-case">(seg)</span>
            </label>
            @if($locked)
                <div class="flex items-center gap-2">
                    <div class="w-16 bg-gray-900/50 border border-gray-700/50 rounded px-2 py-2 text-gray-400 text-sm font-mono text-center">
                        {{ $segment->duration_seconds }}
                    </div>
                    <span class="text-gray-600 text-xs font-mono">
                        {{ sprintf('%02d:%02d', floor($segment->duration_seconds / 60), $segment->duration_seconds % 60) }}
                    </span>
                </div>
            @else
            <div class="flex items-center gap-2">
                <input
                    type="number"
                    name="duration_seconds"
                    value="{{ $segment->duration_seconds }}"
                    hx-post="/segment/{{ $segment->id }}/update-field"
                    hx-trigger="keyup[key=='Enter'], blur"
                    hx-target="#tabla-segmentos"
                    hx-swap="innerHTML"
                    onkeydown="if(event.key==='Enter') this.blur()"
                    style="-moz-appearance:textfield;"
                    class="w-16 bg-gray-900 border border-gray-700 rounded px-2 py-2 text-white text-sm font-mono text-center
                           focus:border-blue-500 focus:outline-none
                           [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                <span class="text-gray-500 text-xs font-mono">
                    {{ sprintf('%02d:%02d', floor($segment->duration_seconds / 60), $segment->duration_seconds % 60) }}
                </span>
            </div>
            @endif
        </div>
    </div>


{{-- ══ MODAL NOTAS DE PRODUCCIÓN ══ --}}
<div id="modal-notas-prod"
     class="hidden fixed inset-0 z-50 flex items-center justify-center"
     onclick="if(event.target===this) cerrarModalNotas()">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
    <div class="relative bg-gray-900 border border-gray-700 rounded-lg shadow-2xl w-full max-w-lg mx-4 z-10">
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-700">
            <div class="flex items-center gap-2">
                <span class="text-amber-400 text-sm">📋</span>
                <span class="text-xs font-bold uppercase tracking-widest text-gray-300">Nota de Producción</span>
                <span id="modal-notas-segnum" class="font-mono text-xs text-amber-400 bg-amber-900/30 px-2 py-0.5 rounded"></span>
            </div>
            <button onclick="cerrarModalNotas()" class="text-gray-600 hover:text-white transition text-sm">✕</button>
        </div>
        <div class="px-5 py-4">
            <textarea
                id="modal-notas-textarea"
                placeholder="Instrucciones técnicas, menciones, comerciales, links, etc."
                class="w-full bg-gray-950 text-gray-200 p-3 rounded border border-gray-700
                       focus:border-amber-500 outline-none resize-none text-sm leading-relaxed
                       hover:border-gray-600 transition placeholder-gray-700 font-mono"
                rows="8"
            ></textarea>
            <div id="modal-notas-indicator" class="text-[10px] text-gray-600 italic mt-1 text-right h-3"></div>
        </div>
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-700">
            <button onclick="borrarNota()"
                class="text-xs text-red-600 hover:text-red-400 transition uppercase tracking-widest font-bold">
                🗑 Borrar nota
            </button>
            <div class="flex gap-2">
                <button onclick="cerrarModalNotas()"
                    class="px-4 py-1.5 rounded text-xs font-bold uppercase tracking-widest
                           bg-gray-800 hover:bg-gray-700 text-gray-400 transition">
                    Cancelar
                </button>
                <button onclick="guardarNota()"
                    class="px-4 py-1.5 rounded text-xs font-bold uppercase tracking-widest
                           bg-amber-600 hover:bg-amber-500 text-white transition">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    let _notaSegmentId = null;

    window.abrirModalNotas = function(segmentId, contenidoActual) {
        _notaSegmentId = segmentId;
        document.getElementById('modal-notas-textarea').value = contenidoActual || '';
        document.getElementById('modal-notas-indicator').textContent = '';
        const segNum = document.querySelector('#properties-panel .font-mono.text-blue-400');
        document.getElementById('modal-notas-segnum').textContent = segNum ? segNum.textContent.trim() : '';
        document.getElementById('modal-notas-prod').classList.remove('hidden');
        setTimeout(() => document.getElementById('modal-notas-textarea').focus(), 80);
    };

    window.cerrarModalNotas = function() {
        document.getElementById('modal-notas-prod').classList.add('hidden');
        _notaSegmentId = null;
    };

    window.guardarNota = async function() {
        if (!_notaSegmentId) return;
        const texto = document.getElementById('modal-notas-textarea').value;
        const ind   = document.getElementById('modal-notas-indicator');
        ind.textContent = 'Guardando...';
        ind.className   = 'text-[10px] text-gray-500 italic mt-1 text-right h-3';
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const res  = await fetch(`/segment/${_notaSegmentId}/update-notes`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `production_notes=${encodeURIComponent(texto)}`
        });
        if (res.ok) {
            ind.textContent = '✓ Guardado';
            ind.className   = 'text-[10px] text-green-500 font-bold mt-1 text-right h-3';
            setTimeout(() => {
                htmx.ajax('GET', `/segment/${_notaSegmentId}/edit`, {
                    target: '#editor-container',
                    swap: 'innerHTML'
                });
                cerrarModalNotas();
            }, 600);
        } else {
            ind.textContent = '✗ Error al guardar';
            ind.className   = 'text-[10px] text-red-500 font-bold mt-1 text-right h-3';
        }
    };

    window.borrarNota = async function() {
        if (!_notaSegmentId) return;
        if (!confirm('¿Borrar la nota de producción de este ítem?')) return;
        document.getElementById('modal-notas-textarea').value = '';
        await guardarNota();
    };

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') cerrarModalNotas();
    });
})();
</script>
</div>
