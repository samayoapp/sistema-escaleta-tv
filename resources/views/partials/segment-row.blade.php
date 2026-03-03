@php
    $typeStyles = [
        'VIVO'            => 'border-l-4 border-l-red-500 bg-red-500/5',
        'VTR'             => 'border-l-4 border-l-green-500 bg-green-500/5',
        'OFF'             => 'border-l-4 border-l-purple-500 bg-purple-500/5',
        'CORTE_COMERCIAL' => 'border-l-4 border-l-yellow-500 bg-yellow-500/5',
        'NOTA_SECA'       => 'border-l-4 border-l-gray-500 bg-gray-500/5',
        'PRESENTACION'    => 'border-l-4 border-l-blue-500 bg-blue-500/5',
        'CIERRE'          => 'border-l-4 border-l-orange-500 bg-orange-500/5',
    ];

    $typeColors = [
        'VIVO'            => 'text-red-400',
        'VTR'             => 'text-green-400',
        'OFF'             => 'text-purple-400',
        'CORTE_COMERCIAL' => 'text-yellow-400',
        'NOTA_SECA'       => 'text-gray-400',
        'PRESENTACION'    => 'text-blue-400',
        'CIERRE'          => 'text-orange-400',
    ];

    $typeLabels = [
        'VIVO'            => '🔴 VIVO',
        'VTR'             => '🎬 VTR',
        'OFF'             => '🎙️ OFF',
        'CORTE_COMERCIAL' => '💰 COMERCIAL',
        'NOTA_SECA'       => '📄 NOTA SECA',
        'PRESENTACION'    => '🎤 PRESENTACIÓN',
        'CIERRE'          => '🏁 CIERRE',
    ];
@endphp

<tr class="hover:bg-gray-700/30 transition-colors border-b border-gray-700/50 {{ $typeStyles[$segment->type] ?? '' }}"
    id="segment-{{ $segment->id }}">
    
    {{-- Drag handle --}}
    <td class="px-4 py-3 w-10">
        <div class="drag-handle cursor-grab active:cursor-grabbing text-gray-600 hover:text-blue-400 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
            </svg>
        </div>
        <input type="hidden" name="segment_ids[]" value="{{ $segment->id }}">
    </td>

    {{-- Orden --}}
    <td class="px-4 py-3 font-mono text-blue-300 text-sm w-12">
        {{ $segment->order_index }}
    </td>

    {{-- Título + Tipo --}}
    <td class="px-4 py-3">
        <input 
            type="text" 
            name="title" 
            value="{{ $segment->title }}"
            hx-post="/segment/{{ $segment->id }}/update-field"
            hx-trigger="keyup changed delay:1s"
            hx-target="#segment-{{ $segment->id }}"
            hx-swap="outerHTML"
            class="bg-transparent border-b border-transparent hover:border-gray-500 focus:border-blue-500 outline-none w-full transition-all py-1 text-sm">

        <select 
            name="type" 
            hx-post="/segment/{{ $segment->id }}/update-field"
            hx-trigger="change"
            hx-target="#segment-{{ $segment->id }}"
            hx-swap="outerHTML"
            class="bg-transparent text-[10px] font-bold uppercase mt-1 {{ $typeColors[$segment->type] ?? 'text-gray-400' }} cursor-pointer focus:outline-none">
            @foreach($typeLabels as $value => $label)
                <option value="{{ $value }}" {{ $segment->type == $value ? 'selected' : '' }}
                    class="bg-gray-800 text-white">
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </td>

    {{-- Duración --}}
    <td class="px-4 py-3 w-32">
        <div class="flex items-center gap-1">
            <input 
                type="number" 
                name="duration_seconds" 
                value="{{ $segment->duration_seconds }}"
                hx-post="/segment/{{ $segment->id }}/update-field"
                hx-trigger="keyup changed delay:1s"
                hx-target="#segment-{{ $segment->id }}"
                hx-swap="outerHTML"
                class="bg-transparent border-b border-transparent hover:border-gray-500 focus:border-blue-500 outline-none w-16 text-center font-mono text-sm">
            <span class="text-xs text-gray-500">seg</span>
        </div>
    </td>

    {{-- Acciones --}}
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
    </td>
</tr>