<tr class="hover:bg-gray-750 transition-colors border-b border-gray-700">
    <td class="px-4 py-4 w-10">
        <div class="drag-handle cursor-grab active:cursor-grabbing text-gray-600 hover:text-blue-400 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
            </svg>
        </div>
    </td>
    <input type="hidden" name="segment_ids[]" value="{{ $segment->id }}">
    <td class="px-6 py-4 font-mono text-blue-300 text-sm">{{ $segment->order_index }}</td>
    
    <td class="px-6 py-4">
        <input type="text" 
            name="title" 
            value="{{ $segment->title }}"
            hx-post="/segment/{{ $segment->id }}/update-field"
            hx-trigger="keyup changed delay:1s"
            class="bg-transparent border-b border-transparent hover:border-gray-500 focus:border-blue-500 outline-none w-full transition-all py-1">
    </td>

    <td class="px-6 py-4">
        <input type="number" 
            name="duration_seconds" 
            value="{{ $segment->duration_seconds }}"
            hx-post="/segment/{{ $segment->id }}/update-field"
            hx-trigger="keyup changed delay:1s"
            class="bg-transparent border-b border-transparent hover:border-gray-500 focus:border-blue-500 outline-none w-16 text-center font-mono">
        <span class="text-xs text-gray-500">seg</span>
    </td>

    <td class="px-6 py-4 text-right flex justify-end gap-2">
        <button 
            hx-get="/segment/{{ $segment->id }}/edit" 
            hx-target="#editor-container"
            class="bg-blue-600 hover:bg-blue-500 px-3 py-1 rounded text-xs transition font-bold uppercase">
            Guion
        </button>
        
        <button 
            hx-delete="/segment/{{ $segment->id }}"
            hx-confirm="¿Seguro que quieres eliminar este bloque?"
            hx-target="#tabla-segmentos" 
            hx-swap="innerHTML"
            class="bg-red-900/50 hover:bg-red-600 text-red-200 p-1 rounded transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </button>
    </td>
</tr>