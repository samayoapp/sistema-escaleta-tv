<span class="text-gray-400 uppercase text-xs font-bold tracking-wider">Duración Total Estimada:</span>
<span class="text-3xl font-mono text-yellow-400 ml-3">
    {{ floor($rundown->segments->sum('duration_seconds') / 60) }} min 
    {{ $rundown->segments->sum('duration_seconds') % 60 }} seg
</span>