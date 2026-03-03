<div class="animate-in fade-in duration-300">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-blue-400">{{ $segment->title }}</h3>
        <div id="save-indicator" class="text-xs text-gray-500 italic"></div>
    </div>
    
    <label class="block text-xs uppercase text-gray-500 font-bold mb-2">Guion Literario</label>
    
    <textarea 
        name="script_content"
        hx-post="/segment/{{ $segment->id }}/update-script"
        hx-trigger="keyup changed delay:800ms" 
        hx-target="#save-indicator"
        class="w-full h-[60vh] bg-gray-900 text-gray-100 p-4 rounded border border-gray-600 focus:border-blue-500 outline-none resize-none font-mono text-lg"
    >{{ $segment->script_content }}</textarea>
</div>