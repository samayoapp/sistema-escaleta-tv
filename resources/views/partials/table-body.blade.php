@forelse($rundown->blocks->sortBy('order_index') as $blockIndex => $block)
@php $blockNum = $blockIndex + 1; @endphp

    {{-- CABECERA DEL BLOQUE --}}
    <tr class="bg-blue-900/30 hover:bg-blue-900/40 transition block-header"
        data-block-id="{{ $block->id }}">

        <td class="px-4 py-2 w-10">
            <button onclick="toggleBlock({{ $block->id }})"
                class="text-blue-400 hover:text-white transition focus:outline-none">
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

        <td class="px-2 py-2">
            <input
                type="text"
                value="{{ $block->title }}"
                name="title"
                hx-post="/block/{{ $block->id }}/update"
                hx-trigger="keyup changed delay:1s"
                class="bg-transparent border-none text-blue-400 font-bold uppercase text-xs focus:ring-0 focus:outline-none w-full tracking-widest cursor-text">
        </td>

        <td class="px-4 py-2 text-right">
            <span class="text-[10px] text-blue-300 font-mono bg-blue-900/40 px-2 py-1 rounded">
                {{ floor($block->segments->sum('duration_seconds') / 60) }}m
                {{ $block->segments->sum('duration_seconds') % 60 }}s
            </span>
        </td>

        <td class="px-4 py-2 text-right">
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
        </td>
    </tr>

    {{-- SEGMENTOS DEL BLOQUE --}}
    @forelse($block->segments->sortBy('order_index') as $segIndex => $segment)
    @php $segNum = "B{$blockNum}." . ($segIndex + 1); @endphp

        <tr class="block-segment segment-of-{{ $block->id }} hover:bg-gray-700/30 transition-colors border-b border-gray-700/30
            {{ match($segment->type) {
                'VIVO'            => 'border-l-4 border-l-red-500 bg-red-500/5',
                'VTR'             => 'border-l-4 border-l-green-500 bg-green-500/5',
                'OFF'             => 'border-l-4 border-l-purple-500 bg-purple-500/5',
                'CORTE_COMERCIAL' => 'border-l-4 border-l-yellow-500 bg-yellow-500/5',
                'NOTA_SECA'       => 'border-l-4 border-l-gray-500 bg-gray-500/5',
                'PRESENTACION'    => 'border-l-4 border-l-blue-400 bg-blue-400/5',
                'CIERRE'          => 'border-l-4 border-l-orange-500 bg-orange-500/5',
                default           => ''
            } }}"
            id="segment-{{ $segment->id }}"
            data-segment-id="{{ $segment->id }}"
            data-block-id="{{ $block->id }}">

            <td class="px-4 py-3 w-10">
                <div class="drag-handle cursor-grab active:cursor-grabbing text-gray-600 hover:text-blue-400 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                    </svg>
                </div>
            </td>

            <td class="px-4 py-3 w-12">
                <span class="font-mono text-blue-300 text-xs">{{ $segNum }}</span>
            </td>

            <td class="px-4 py-3">
                <input
                    type="text"
                    name="title"
                    value="{{ $segment->title }}"
                    hx-post="/segment/{{ $segment->id }}/update-field"
                    hx-trigger="keyup changed delay:1s"
                    hx-target="#tabla-segmentos"
                    hx-swap="innerHTML"
                    class="bg-transparent border-b border-transparent hover:border-gray-500 focus:border-blue-500 outline-none w-full transition-all py-1 text-sm">

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
            </td>

            <td class="px-4 py-3 w-32">
                <div class="flex items-center gap-1">
                    <input
                        type="number"
                        name="duration_seconds"
                        value="{{ $segment->duration_seconds }}"
                        hx-post="/segment/{{ $segment->id }}/update-field"
                        hx-trigger="keyup changed delay:1s"
                        hx-target="#tabla-segmentos"
                        hx-swap="innerHTML"
                        class="bg-transparent border-b border-transparent hover:border-gray-500 focus:border-blue-500 outline-none w-16 text-center font-mono text-sm">
                    <span class="text-xs text-gray-500">seg</span>
                </div>
            </td>

            <td class="px-4 py-3 text-right">
                <div class="flex justify-end gap-2">
                    <button
                        hx-get="/segment/{{ $segment->id }}/edit"
                        hx-target="#editor-container"
                        class="bg-blue-600 hover:bg-blue-500 px-3 py-1 rounded text-xs transition font-bold uppercase">
                        ✏️ Guion
                    </button>
                    <button
                        hx-delete="/segment/{{ $segment->id }}"
                        hx-confirm="¿Eliminar este segmento?"
                        hx-target="#tabla-segmentos"
                        hx-swap="innerHTML"
                        class="bg-red-900/50 hover:bg-red-600 text-red-300 p-1 rounded transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </td>
        </tr>

    @empty
        <tr class="segment-of-{{ $block->id }} empty-block-{{ $block->id }}">
            <td colspan="5" class="px-12 py-3 text-gray-600 italic text-xs">
                Sin ítems. Haz clic en "+ Ítem" para agregar.
            </td>
        </tr>
    @endforelse

    {{-- Separador visual entre bloques --}}
    <tr class="h-1 bg-gray-900/50"><td colspan="5"></td></tr>

@empty
    <tr>
        <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">
            No hay bloques. Haz clic en "Nuevo Bloque" para comenzar.
        </td>
    </tr>
@endforelse
