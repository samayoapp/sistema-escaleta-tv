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

    {{-- NOTAS DE PRODUCCIÓN --}}
    <div class="mb-4">
        <label class="text-[10px] uppercase text-gray-500 font-bold tracking-widest block mb-1">
            📋 Notas de Producción
        </label>
        @if($locked)
            <div class="w-full bg-gray-900/50 border border-gray-700/50 rounded p-3 text-gray-400 text-xs leading-relaxed whitespace-pre-wrap min-h-[4rem]">
                {{ $segment->production_notes ?: '—' }}
            </div>
        @else
            <div id="notes-indicator" class="text-[10px] text-gray-600 italic mb-1 text-right h-3"></div>
            <textarea
                name="production_notes"
                hx-post="/segment/{{ $segment->id }}/update-notes"
                hx-trigger="keyup changed delay:600ms"
                hx-target="#notes-indicator"
                placeholder="Instrucciones técnicas, menciones, comerciales, etc."
                class="w-full bg-gray-900 text-gray-300 p-3 rounded border border-gray-700
                       focus:border-amber-500 outline-none resize-none text-xs leading-relaxed
                       hover:border-gray-500 transition placeholder-gray-700"
                rows="4"
            >{{ $segment->production_notes }}</textarea>
        @endif
    </div>

    {{-- SEPARADOR --}}
    <div class="border-t border-gray-700/60 mb-4"></div>

    {{-- TIPO + DURACIÓN --}}
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="text-[10px] uppercase text-gray-500 font-bold tracking-widest block mb-1">Tipo</label>
            @if($locked)
                @php
                    $tipoColor = match($segment->type) {
                        'VIVO'            => 'text-red-400',
                        'VTR'             => 'text-green-400',
                        'OFF'             => 'text-purple-400',
                        'CORTE_COMERCIAL' => 'text-yellow-400',
                        'NOTA_SECA'       => 'text-gray-400',
                        'PRESENTACION'    => 'text-blue-400',
                        'CIERRE'          => 'text-orange-400',
                        default           => 'text-gray-400'
                    };
                @endphp
                <div class="w-full bg-gray-900/50 border border-gray-700/50 rounded px-2 py-2 text-xs {{ $tipoColor }}">
                    {{ $segment->type }}
                </div>
            @else
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

</div>
