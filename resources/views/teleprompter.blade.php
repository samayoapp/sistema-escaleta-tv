<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROMPTER: {{ $rundown->show->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body::-webkit-scrollbar { display: none; }
        .prompter-text {
            line-height: 1.5;
        }
        .block-divider {
            background: linear-gradient(90deg, transparent, #1d4ed8, #1d4ed8, transparent);
        }
    </style>
</head>
<body class="bg-black text-white overflow-y-scroll">

@php
    $totalSeconds = $rundown->blocks->flatMap->segments->sum('duration_seconds');
    $totalMin = floor($totalSeconds / 60);
    $totalSeg = $totalSeconds % 60;
@endphp

{{-- HEADER FIJO --}}
<div class="sticky top-0 z-50 bg-black/90 backdrop-blur border-b border-gray-800 px-16 py-4 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-blue-400 uppercase tracking-widest">
            {{ $rundown->show->title }}
        </h1>
        <p class="text-gray-500 text-sm">
            Emisión: {{ \Carbon\Carbon::parse($rundown->air_date)->format('d/m/Y') }}
        </p>
    </div>
    <div class="text-right">
        <div class="text-xs text-gray-500 uppercase tracking-widest mb-1">Duración Total</div>
        <div class="text-2xl font-mono text-yellow-400">{{ $totalMin }}m {{ $totalSeg }}s</div>
    </div>
    {{-- Controles de velocidad --}}
    <div class="flex items-center gap-3">
        <button onclick="changeSpeed(-1)" class="bg-gray-800 hover:bg-gray-700 px-3 py-2 rounded text-sm font-mono">− Vel</button>
        <span id="speed-label" class="text-xs text-gray-400 w-16 text-center">Pausado</span>
        <button onclick="changeSpeed(1)" class="bg-gray-800 hover:bg-gray-700 px-3 py-2 rounded text-sm font-mono">+ Vel</button>
        <button onclick="toggleScroll()" id="btn-play"
            class="bg-blue-700 hover:bg-blue-600 px-4 py-2 rounded text-sm font-bold uppercase tracking-widest">
            ▶ Play
        </button>
    </div>
</div>

{{-- CONTENIDO --}}
<div class="w-full px-16 py-16">

    @forelse($rundown->blocks->sortBy('order_index') as $blockIndex => $block)
    @php $blockNum = $blockIndex + 1; @endphp

        {{-- SEPARADOR DE BLOQUE --}}
        <div class="my-20">
            <div class="block-divider h-1 w-full mb-6"></div>
            <div class="flex items-center gap-4 mb-2">
                <span class="text-blue-600 font-mono font-bold text-lg">B{{ $blockNum }}</span>
                <h2 class="text-blue-400 font-bold uppercase tracking-widest text-xl">
                    {{ $block->title }}
                </h2>
                <span class="text-gray-600 text-sm font-mono ml-auto">
                    {{ floor($block->segments->sum('duration_seconds') / 60) }}m
                    {{ $block->segments->sum('duration_seconds') % 60 }}s
                </span>
            </div>
            <div class="block-divider h-px w-full"></div>
        </div>

        {{-- SEGMENTOS DEL BLOQUE --}}
        @forelse($block->segments->sortBy('order_index') as $segIndex => $segment)
        @php
            $segNum = "B{$blockNum}." . ($segIndex + 1);
            $typeColors = [
                'VIVO'            => 'text-red-500',
                'VTR'             => 'text-green-500',
                'OFF'             => 'text-purple-400',
                'CORTE_COMERCIAL' => 'text-yellow-500',
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
            $typeColor = $typeColors[$segment->type] ?? 'text-gray-400';
            $typeLabel = $typeLabels[$segment->type] ?? $segment->type;
        @endphp

            <div class="mb-24">

                {{-- Cabecera del segmento --}}
                <div class="flex items-center gap-4 mb-6 border-b border-gray-800 pb-3">
                    <span class="font-mono text-blue-300 text-base">{{ $segNum }}</span>
                    <span class="text-gray-500 font-bold text-2xl uppercase tracking-wide flex-1">
                        {{ $segment->title }}
                    </span>
                    <span class="text-sm font-bold {{ $typeColor }} uppercase">
                        {{ $typeLabel }}
                    </span>
                    <span class="text-gray-600 font-mono text-sm">
                        {{ floor($segment->duration_seconds / 60) }}m {{ $segment->duration_seconds % 60 }}s
                    </span>
                </div>

                {{-- Guion --}}
                @if($segment->script_content)
                    <div class="prompter-text text-8xl font-bold leading-tight text-white">
                        {!! nl2br(e($segment->script_content)) !!}
                    </div>
                @else
                    <div class="text-gray-700 italic text-2xl">
                        — Sin guion literario —
                    </div>
                @endif

            </div>

        @empty
            <div class="text-gray-700 italic text-xl mb-16 px-4">
                — Bloque sin ítems —
            </div>
        @endforelse

    @empty
        <div class="text-gray-600 italic text-center py-32 text-2xl">
            No hay contenido en este rundown.
        </div>
    @endforelse

    {{-- FIN --}}
    <div class="my-32">
        <div class="block-divider h-1 w-full mb-8"></div>
        <div class="text-gray-600 text-center py-10 text-3xl italic tracking-widest">
            ★ FIN DEL PROGRAMA ★
        </div>
        <div class="block-divider h-1 w-full mt-8"></div>
    </div>

</div>

<script>
    let scrolling = false;
    let speed = 2; // px por frame
    let animFrame = null;

    function scroll() {
        if (!scrolling) return;
        window.scrollBy(0, speed);
        animFrame = requestAnimationFrame(scroll);
    }

    function toggleScroll() {
        scrolling = !scrolling;
        const btn = document.getElementById('btn-play');
        if (scrolling) {
            btn.textContent = '⏸ Pausa';
            btn.classList.replace('bg-blue-700', 'bg-red-700');
            btn.classList.replace('hover:bg-blue-600', 'hover:bg-red-600');
            animFrame = requestAnimationFrame(scroll);
        } else {
            btn.textContent = '▶ Play';
            btn.classList.replace('bg-red-700', 'bg-blue-700');
            btn.classList.replace('hover:bg-red-600', 'hover:bg-blue-600');
            cancelAnimationFrame(animFrame);
        }
        updateSpeedLabel();
    }

    function changeSpeed(delta) {
        speed = Math.max(1, Math.min(10, speed + delta));
        updateSpeedLabel();
    }

    function updateSpeedLabel() {
        document.getElementById('speed-label').textContent = scrolling ? `Vel: ${speed}` : 'Pausado';
    }

    // Teclado
    window.addEventListener('keydown', (e) => {
        if (e.key === ' ') { e.preventDefault(); toggleScroll(); }
        if (e.key === 'ArrowUp')   changeSpeed(-1);
        if (e.key === 'ArrowDown') changeSpeed(1);
    });
</script>

</body>
</html>
