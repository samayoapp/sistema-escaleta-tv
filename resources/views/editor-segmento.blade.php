<div class="flex flex-col gap-0" id="properties-panel">

    {{-- HEADER --}}
    <div class="flex items-center justify-between border-b border-gray-700 pb-3 mb-4">
        <div class="flex items-center gap-2">
            <span class="font-mono text-xs font-bold text-blue-400 bg-blue-900/40 px-2 py-1 rounded">
                {{ $segNum ?? '—' }}
            </span>
            <span class="text-[10px] uppercase text-gray-500 tracking-widest">Propiedades</span>
        </div>
        <button onclick="deseleccionarSegmento()"
            class="text-gray-600 hover:text-white transition text-sm leading-none">✕</button>
    </div>

    {{-- 1. TÍTULO --}}
    <div class="mb-4">
        <label class="text-[10px] uppercase text-gray-500 font-bold tracking-widest block mb-1">Título</label>
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
    </div>

    {{-- 2. GUION LITERARIO (al tope, es lo más importante) --}}
    <div class="mb-5">
        <div class="flex items-center justify-between mb-2">
            <label class="text-[10px] uppercase text-gray-500 font-bold tracking-widest">Guion Literario</label>
            <label class="flex items-center gap-2 cursor-pointer group">
                <span class="text-[10px] text-gray-600 group-hover:text-gray-400 transition">Activar</span>
                <input
                    type="checkbox"
                    {{ $segment->has_script ? 'checked' : '' }}
                    hx-post="/segment/{{ $segment->id }}/toggle-script"
                    hx-target="#editor-container"
                    hx-swap="innerHTML"
                    class="w-3.5 h-3.5 rounded accent-blue-500 cursor-pointer">
            </label>
        </div>

        @if($segment->has_script)
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
        @else
            <div class="bg-gray-900/50 border border-dashed border-gray-700 rounded p-4 text-center">
                <p class="text-xs text-gray-600 italic">Sin guion literario.</p>
                <p class="text-[10px] text-gray-700 mt-1">Activa el toggle para agregar texto.</p>
            </div>
        @endif
    </div>

    {{-- SEPARADOR --}}
    <div class="border-t border-gray-700/60 mb-4"></div>

    {{-- 3. TIPO + DURACIÓN (abajo, ya editables en tabla) --}}
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="text-[10px] uppercase text-gray-500 font-bold tracking-widest block mb-1">Tipo</label>
            <select
                name="type"
                hx-post="/segment/{{ $segment->id }}/update-field"
                hx-trigger="change"
                hx-target="#tabla-segmentos"
                hx-swap="innerHTML"
                class="w-full bg-gray-900 border border-gray-700 rounded px-2 py-2 text-xs
                       focus:border-blue-500 focus:outline-none cursor-pointer
                       {{ match($segment->type) {
                           'VIVO'            => 'text-red-400',
                           'VTR'             => 'text-green-400',
                           'OFF'             => 'text-purple-400',
                           'CORTE_COMERCIAL' => 'text-yellow-400',
                           'NOTA_SECA'       => 'text-gray-400',
                           'PRESENTACION'    => 'text-blue-400',
                           'CIERRE'          => 'text-orange-400',
                           default           => 'text-gray-400'
                       } }}">
                <option value="VIVO"            {{ $segment->type == 'VIVO'            ? 'selected' : '' }} class="bg-gray-800 text-red-400">🔴 VIVO</option>
                <option value="VTR"             {{ $segment->type == 'VTR'             ? 'selected' : '' }} class="bg-gray-800 text-green-400">🎬 VTR</option>
                <option value="OFF"             {{ $segment->type == 'OFF'             ? 'selected' : '' }} class="bg-gray-800 text-purple-400">🎙️ OFF</option>
                <option value="CORTE_COMERCIAL" {{ $segment->type == 'CORTE_COMERCIAL' ? 'selected' : '' }} class="bg-gray-800 text-yellow-400">💰 COMERCIAL</option>
                <option value="NOTA_SECA"       {{ $segment->type == 'NOTA_SECA'       ? 'selected' : '' }} class="bg-gray-800 text-gray-400">📄 NOTA SECA</option>
                <option value="PRESENTACION"    {{ $segment->type == 'PRESENTACION'    ? 'selected' : '' }} class="bg-gray-800 text-blue-400">🎤 PRESENTACIÓN</option>
                <option value="CIERRE"          {{ $segment->type == 'CIERRE'          ? 'selected' : '' }} class="bg-gray-800 text-orange-400">🏁 CIERRE</option>
            </select>
        </div>

        <div>
            <label class="text-[10px] uppercase text-gray-500 font-bold tracking-widest block mb-1">
                Duración <span class="text-gray-700 normal-case">(seg)</span>
            </label>
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
        </div>
    </div>

</div>
