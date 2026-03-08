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

    {{-- TÍTULO --}}
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

    {{-- TOGGLES: Guion + Teleprompter en la misma fila --}}
    <div class="flex gap-4 mb-3">

        {{-- Guion literario --}}
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

        {{-- Teleprompter --}}
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

    </div>

    {{-- GUION LITERARIO --}}
    @if($segment->has_script)
        <div class="mb-5">
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
        </div>
    @else
        <div class="bg-gray-900/50 border border-dashed border-gray-700 rounded p-3 text-center mb-5">
            <p class="text-xs text-gray-600 italic">Sin guion literario activado.</p>
        </div>
    @endif

    {{-- SEPARADOR --}}
    <div class="border-t border-gray-700/60 mb-4"></div>

    {{-- TIPO + DURACIÓN --}}
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
