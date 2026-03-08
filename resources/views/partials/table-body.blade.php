@php
    $airTimeParts = explode(':', $rundown->air_time ?? '19:00:00');
    $acumulado = ((int)$airTimeParts[0] * 3600) + ((int)$airTimeParts[1] * 60) + ((int)($airTimeParts[2] ?? 0));

    function fmtDuration($s) {
        return sprintf('%02d:%02d', floor($s / 60), $s % 60);
    }
    function fmtHora($s) {
        $s = $s % 86400;
        return sprintf('%02d:%02d:%02d', floor($s / 3600), floor(($s % 3600) / 60), $s % 60);
    }
@endphp

@forelse($rundown->blocks->sortBy('order_index') as $blockIndex => $block)
@php
    $blockNum   = $blockIndex + 1;
    $blockStart = $acumulado;
    $segments   = $block->segments->sortBy('order_index');
@endphp

    {{-- CABECERA DEL BLOQUE --}}
    <tr class="bg-blue-900/30 hover:bg-blue-900/40 transition block-header"
        data-block-id="{{ $block->id }}">

        <td class="px-4 py-2 w-10">
            <button onclick="toggleBlock({{ $block->id }})"
                class="text-blue-400 hover:text-white transition focus:outline-none {{ $locked ? 'opacity-40 cursor-not-allowed' : '' }}"
                {{ $locked ? 'disabled' : '' }}>
                <svg id="arrow-{{ $block->id }}"
                    class="h-4 w-4 transform transition-transform duration-200 rotate-90"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </td>

        <td class="px-2 py-2 w-12">
            <span class="text-[10px] font-mono font-bold text-blue-500 bg-blue-900/50 px-2 py-1 rounded">
                B{{ $blockNum }}
            </span>
        </td>

        <td class="px-2 py-2 col-titulo-bloque">
            @if($locked)
                <span class="text-blue-400 font-bold uppercase text-xs tracking-widest">{{ $block->title }}</span>
            @else
                <input
                    type="text"
                    value="{{ $block->title }}"
                    name="title"
                    hx-post="/block/{{ $block->id }}/update"
                    hx-trigger="keyup[key=='Enter'], blur"
                    hx-swap="none"
                    onkeydown="if(event.key==='Enter') this.blur()"
                    class="bg-transparent border-b border-transparent text-blue-400 font-bold uppercase text-xs
                           focus:ring-0 focus:outline-none focus:border-blue-400 focus:bg-blue-900/40
                           focus:px-2 focus:rounded w-full tracking-widest cursor-text transition-all duration-150
                           hover:border-blue-700">
            @endif
        </td>

        <td class="px-4 py-2 text-right w-24">
            <span class="text-[10px] text-blue-300 font-mono bg-blue-900/40 px-2 py-1 rounded">
                {{ fmtDuration($block->segments->sum('duration_seconds')) }}
            </span>
        </td>

        <td class="px-4 py-2 text-center w-28">
            <span class="text-[10px] text-blue-400 font-mono bg-blue-900/20 px-2 py-1 rounded">
                ▶ {{ fmtHora($blockStart) }}
            </span>
        </td>

        <td class="px-4 py-2 text-right">
            @if(!$locked)
            <div class="flex justify-end gap-2">
                <button
                    hx-post="/block/{{ $block->id }}/add-segment"
                    hx-target="#tabla-segmentos"
                    hx-swap="innerHTML"
                    class="text-green-400 hover:text-green-300 text-xs font-bold uppercase transition px-2 py-1 rounded border border-green-800 hover:border-green-600">
                    + Ítem
                </button>
                <button
                    hx-delete="/block/{{ $block->id }}"
                    hx-confirm="¿Eliminar este bloque y todos sus ítems?"
                    hx-target="#tabla-segmentos"
                    hx-swap="innerHTML"
                    class="text-red-400 hover:text-red-300 text-xs transition px-2 py-1 rounded border border-red-900 hover:border-red-700">
                    🗑
                </button>
            </div>
            @endif
        </td>
    </tr>

    {{-- SEGMENTOS DEL BLOQUE --}}
    @forelse($segments as $segIndex => $segment)
    @php
        $segNum  = "B{$blockNum}." . ($segIndex + 1);
        $horaFin = $acumulado + $segment->duration_seconds;
        $acumulado += $segment->duration_seconds;
    @endphp

        {{-- FILA INSERTAR ANTES (solo primera posición) --}}
        @if(!$locked && $segIndex === 0)
        <tr class="insert-row h-0 overflow-hidden group/insert" data-after-segment="0" data-block-id="{{ $block->id }}">
            <td colspan="6" class="p-0">
                <div class="h-0 group-hover/insert:h-6 overflow-hidden transition-all duration-150 flex items-center justify-center">
                    <button
                        onclick="event.stopPropagation(); insertarItemDespues(0, {{ $block->id }})"
                        class="w-full h-5 flex items-center justify-center gap-1
                               text-[10px] font-bold text-green-400 bg-green-900/20 hover:bg-green-900/40
                               border-y border-dashed border-green-800/40 hover:border-green-600 transition">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                        </svg>
                        Insertar ítem aquí
                    </button>
                </div>
            </td>
        </tr>
        @endif

        {{-- FILA DE SEGMENTO --}}
        <tr class="block-segment segment-of-{{ $block->id }} transition-colors border-b border-gray-700/30
            {{ $locked ? 'opacity-60' : '' }}
            {{ match($segment->type) {
                'VIVO'            => 'border-l-4 border-l-red-500 bg-red-500/5 hover:bg-red-500/10',
                'VTR'             => 'border-l-4 border-l-green-500 bg-green-500/5 hover:bg-green-500/10',
                'OFF'             => 'border-l-4 border-l-purple-500 bg-purple-500/5 hover:bg-purple-500/10',
                'CORTE_COMERCIAL' => 'border-l-4 border-l-yellow-500 bg-yellow-500/5 hover:bg-yellow-500/10',
                'NOTA_SECA'       => 'border-l-4 border-l-gray-500 bg-gray-500/5 hover:bg-gray-500/10',
                'PRESENTACION'    => 'border-l-4 border-l-blue-400 bg-blue-400/5 hover:bg-blue-400/10',
                'CIERRE'          => 'border-l-4 border-l-orange-500 bg-orange-500/5 hover:bg-orange-500/10',
                default           => 'hover:bg-gray-700/30'
            } }}"
            id="segment-{{ $segment->id }}"
            data-segment-id="{{ $segment->id }}"
            data-block-id="{{ $block->id }}"
            data-seg-num="{{ $segNum }}"
            onclick="seleccionarSegmento({{ $segment->id }}, this)">

            {{-- Drag handle --}}
            <td class="px-4 py-3 w-10" onclick="event.stopPropagation()">
                @if(!$locked)
                <div class="drag-handle cursor-grab active:cursor-grabbing text-gray-600 hover:text-blue-400 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                    </svg>
                </div>
                @endif
            </td>

            {{-- Código --}}
            <td class="px-4 py-3 w-12">
                <span class="font-mono text-blue-300 text-xs">{{ $segNum }}</span>
            </td>

            {{-- Título + Tipo --}}
            <td class="px-4 py-3" onclick="event.stopPropagation()">
                @if($locked)
                    <div class="font-medium text-sm text-gray-400">{{ $segment->title }}</div>
                    <div class="text-[10px] font-bold uppercase mt-0.5 text-gray-600">
                        {{ $segment->type }}
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
                        class="seg-title-input bg-transparent border-b border-transparent
                               hover:border-gray-500 focus:border-blue-400 focus:bg-gray-700/40 focus:px-2 focus:rounded
                               outline-none w-full transition-all py-1 text-sm text-white">
                    <select
                        name="type"
                        hx-post="/segment/{{ $segment->id }}/update-field"
                        hx-trigger="change"
                        hx-target="#tabla-segmentos"
                        hx-swap="innerHTML"
                        class="bg-transparent text-[10px] font-bold uppercase mt-1 cursor-pointer focus:outline-none
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
                        <option value="VIVO"            {{ $segment->type == 'VIVO'            ? 'selected' : '' }} class="bg-gray-800">🔴 VIVO</option>
                        <option value="VTR"             {{ $segment->type == 'VTR'             ? 'selected' : '' }} class="bg-gray-800">🎬 VTR</option>
                        <option value="OFF"             {{ $segment->type == 'OFF'             ? 'selected' : '' }} class="bg-gray-800">🎙️ OFF</option>
                        <option value="CORTE_COMERCIAL" {{ $segment->type == 'CORTE_COMERCIAL' ? 'selected' : '' }} class="bg-gray-800">💰 COMERCIAL</option>
                        <option value="NOTA_SECA"       {{ $segment->type == 'NOTA_SECA'       ? 'selected' : '' }} class="bg-gray-800">📄 NOTA SECA</option>
                        <option value="PRESENTACION"    {{ $segment->type == 'PRESENTACION'    ? 'selected' : '' }} class="bg-gray-800">🎤 PRESENTACIÓN</option>
                        <option value="CIERRE"          {{ $segment->type == 'CIERRE'          ? 'selected' : '' }} class="bg-gray-800">🏁 CIERRE</option>
                    </select>
                    @if($segment->has_script)
                        <span class="text-[9px] text-blue-500/60 ml-1">· guion</span>
                    @endif
                    @if($segment->in_prompter)
                        <span class="text-[9px] text-yellow-500/60 ml-1">· prompter</span>
                    @endif
                @endif
            </td>

            {{-- Duración --}}
            <td class="px-4 py-3 w-28" onclick="event.stopPropagation()">
                <div class="flex flex-col items-center">
                    @if($locked)
                        <span class="font-mono text-gray-400 text-sm">{{ fmtDuration($segment->duration_seconds) }}</span>
                    @else
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
                            class="bg-transparent border-b border-transparent hover:border-gray-500
                                   focus:border-blue-400 outline-none w-16 text-center font-mono text-sm
                                   [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        <span class="text-[10px] text-gray-500 font-mono mt-0.5">
                            {{ fmtDuration($segment->duration_seconds) }}
                        </span>
                    @endif
                </div>
            </td>

            {{-- Hora al aire --}}
            <td class="px-4 py-3 w-28 text-center">
                <span class="font-mono text-yellow-400 text-xs bg-yellow-400/10 px-2 py-1 rounded">
                    {{ fmtHora($horaFin) }}
                </span>
            </td>

            {{-- Eliminar --}}
            <td class="px-4 py-3 text-right" onclick="event.stopPropagation()">
                @if(!$locked)
                <button
                    hx-delete="/segment/{{ $segment->id }}"
                    hx-confirm="¿Eliminar este segmento?"
                    hx-target="#tabla-segmentos"
                    hx-swap="innerHTML"
                    class="bg-red-900/50 hover:bg-red-600 text-red-300 p-1 rounded transition opacity-30 hover:opacity-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
                @endif
            </td>
        </tr>

        {{-- FILA INSERTAR DESPUÉS (entre ítems y al final del bloque) --}}
        @if(!$locked)
        <tr class="insert-row h-0 overflow-hidden group/insert">
            <td colspan="6" class="p-0">
                <div class="h-0 group-hover/insert:h-6 overflow-hidden transition-all duration-150 flex items-center justify-center">
                    <button
                        onclick="event.stopPropagation(); insertarItemDespues({{ $segment->id }}, {{ $block->id }})"
                        class="w-full h-5 flex items-center justify-center gap-1
                               text-[10px] font-bold text-green-400 bg-green-900/20 hover:bg-green-900/40
                               border-y border-dashed border-green-800/40 hover:border-green-600 transition">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                        </svg>
                        Insertar ítem aquí
                    </button>
                </div>
            </td>
        </tr>
        @endif

    @empty
        <tr class="segment-of-{{ $block->id }} empty-block-{{ $block->id }}">
            <td colspan="6" class="px-12 py-3 text-gray-600 italic text-xs">
                Sin ítems. Haz clic en "+ Ítem" para agregar.
            </td>
        </tr>
    @endforelse

    <tr class="h-1 bg-gray-900/50"><td colspan="6"></td></tr>

@empty
    <tr>
        <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">
            No hay bloques. Haz clic en "Nuevo Bloque" para comenzar.
        </td>
    </tr>
@endforelse
