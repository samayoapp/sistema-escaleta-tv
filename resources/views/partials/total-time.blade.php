@php
    $totalSeconds = $rundown->blocks->flatMap->segments->sum('duration_seconds');
    $minutes = floor($totalSeconds / 60);
    $seconds = $totalSeconds % 60;
@endphp

<span class="text-gray-400 uppercase text-xs font-bold tracking-wider">Duración Total Estimada:</span>
<span class="text-3xl font-mono text-yellow-400 ml-3">
    {{ $minutes }} min {{ $seconds }} seg
</span>