<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            margin: 3cm 3cm 3cm 3cm;
            size: letter portrait;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #1a1a1a;
            line-height: 1.6;
            background: white;
            margin: 0;
            padding: 0;
        }

        /* Contenedor principal con márgenes generosos */
        .contenido {
            padding: 60px 80px;
        }

        /* ── HEADER FIJO ── */
        .page-header {
            position: fixed;
            top: 0; left: 0; right: 0;
            padding: 16px 80px 10px;
            border-bottom: 1.5px solid #cbd5e1;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            background: white;
        }
        .page-header .show-name {
            font-size: 8pt;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .page-header .show-date {
            font-size: 7.5pt;
            color: #94a3b8;
            margin-top: 2px;
        }
        .page-header .label {
            font-size: 7.5pt;
            color: #94a3b8;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* ── FOOTER FIJO ── */
        .page-footer {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            padding: 8px 80px 14px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 7pt;
            color: #cbd5e1;
            letter-spacing: 1px;
            text-transform: uppercase;
            background: white;
        }

        /* ── PORTADA ── */
        .portada {
            text-align: center;
            padding: 80px 40px 70px;
            page-break-after: always;
        }
        .portada .label {
            font-size: 8pt;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 4px;
            margin-bottom: 24px;
        }
        .portada h1 {
            font-size: 26pt;
            font-weight: bold;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        .portada .linea {
            width: 60px;
            height: 3px;
            background: #3b82f6;
            margin: 0 auto 26px;
        }
        .portada .meta {
            font-size: 10.5pt;
            color: #64748b;
            margin-bottom: 8px;
        }
        .portada .duracion-badge {
            display: inline-block;
            margin-top: 30px;
            border: 1.5px solid #bfdbfe;
            color: #3b82f6;
            font-size: 10pt;
            font-weight: bold;
            padding: 8px 26px;
            letter-spacing: 1px;
        }

        /* ── CABECERA DE BLOQUE ── */
        .bloque {
            margin-bottom: 10px;
        }
        .bloque-header {
            margin-top: 40px;
            margin-bottom: 20px;
            padding: 10px 16px;
            background-color: #334155;
            display: flex;
            align-items: center;
            gap: 12px;
            page-break-after: avoid;
        }
        .bloque-codigo {
            font-size: 8.5pt;
            font-weight: bold;
            color: #93c5fd;
            background: rgba(255,255,255,0.08);
            padding: 2px 9px;
            letter-spacing: 1px;
        }
        .bloque-titulo {
            font-size: 10pt;
            font-weight: bold;
            color: #e2e8f0;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            flex: 1;
        }
        .bloque-duracion {
            font-size: 7.5pt;
            color: #94a3b8;
        }

        /* ── SEGMENTO ── */
        .segmento {
            margin-bottom: 28px;
            page-break-inside: avoid;
        }
        .segmento-cabecera {
            display: flex;
            align-items: baseline;
            gap: 10px;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
        }
        .seg-codigo {
            font-size: 8pt;
            font-weight: bold;
            color: #3b82f6;
            background: #eff6ff;
            padding: 1px 8px;
            white-space: nowrap;
        }
        .seg-titulo {
            font-size: 10.5pt;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            flex: 1;
        }
        .seg-tipo {
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 1px 7px;
            white-space: nowrap;
        }
        .seg-duracion {
            font-size: 7.5pt;
            color: #94a3b8;
            white-space: nowrap;
        }

        .tipo-VIVO            { background: #fee2e2; color: #b91c1c; }
        .tipo-VTR             { background: #dcfce7; color: #15803d; }
        .tipo-OFF             { background: #f3e8ff; color: #7e22ce; }
        .tipo-CORTE_COMERCIAL { background: #fef9c3; color: #854d0e; }
        .tipo-NOTA_SECA       { background: #f1f5f9; color: #475569; }
        .tipo-PRESENTACION    { background: #dbeafe; color: #1d4ed8; }
        .tipo-CIERRE          { background: #ffedd5; color: #c2410c; }

        /* ── GUION LITERARIO ── */
        .guion-wrapper {
            margin-left: 40px;
            padding-left: 18px;
            border-left: 2px solid #e2e8f0;
        }
        .guion {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 11.5pt;
            line-height: 2;
            color: #0f172a;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .sin-guion {
            font-size: 8.5pt;
            color: #cbd5e1;
            font-style: italic;
            margin-left: 40px;
            padding: 4px 0;
        }

        /* ── CORTE COMERCIAL ── */
        .corte-comercial {
            text-align: center;
            margin: 24px 0;
            padding: 10px 16px;
            border-top: 1px dashed #f59e0b;
            border-bottom: 1px dashed #f59e0b;
            color: #92400e;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            background: #fffbeb;
        }

        /* ── FIN ── */
        .fin {
            text-align: center;
            margin-top: 60px;
            padding: 20px;
            border-top: 1.5px solid #cbd5e1;
            color: #94a3b8;
            font-size: 8pt;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

@php
    $totalSeconds = $rundown->blocks->flatMap->segments->sum('duration_seconds');
    $totalMin     = floor($totalSeconds / 60);
    $totalSeg     = $totalSeconds % 60;

    $typeLabels = [
        'VIVO'            => 'VIVO',
        'VTR'             => 'VTR',
        'OFF'             => 'OFF',
        'CORTE_COMERCIAL' => 'COMERCIAL',
        'NOTA_SECA'       => 'NOTA SECA',
        'PRESENTACION'    => 'PRESENTACIÓN',
        'CIERRE'          => 'CIERRE',
    ];
@endphp

{{-- HEADER FIJO --}}
<div class="page-header">
    <div>
        <div class="show-name">{{ $rundown->show->title }}</div>
        <div class="show-date">{{ \Carbon\Carbon::parse($rundown->air_date)->format('d/m/Y') }}</div>
    </div>
    <div class="label">Guion Literario</div>
</div>

{{-- FOOTER FIJO --}}
<div class="page-footer">
    {{ $rundown->show->title }} &nbsp;·&nbsp; {{ \Carbon\Carbon::parse($rundown->air_date)->format('d/m/Y') }} &nbsp;·&nbsp; Uso Interno
</div>

{{-- CONTENIDO --}}
<div class="contenido">

    {{-- PORTADA --}}
    <div class="portada">
        <div class="label">Guion Literario</div>
        <h1>{{ $rundown->show->title }}</h1>
        <div class="linea"></div>
        <div class="meta">Fecha de emisión: {{ \Carbon\Carbon::parse($rundown->air_date)->format('d \d\e F \d\e Y') }}</div>
        <div class="meta">Estado: {{ ucfirst($rundown->status) }}</div>
        <div class="duracion-badge">Duración estimada: {{ $totalMin }} min {{ $totalSeg }} seg</div>
    </div>

    {{-- BLOQUES --}}
    @foreach($rundown->blocks->sortBy('order_index') as $blockIndex => $block)
    @php $blockNum = $blockIndex + 1; @endphp

        <div class="bloque">

            <div class="bloque-header">
                <span class="bloque-codigo">B{{ $blockNum }}</span>
                <span class="bloque-titulo">{{ $block->title }}</span>
                <span class="bloque-duracion">
                    {{ floor($block->segments->sum('duration_seconds') / 60) }}m
                    {{ $block->segments->sum('duration_seconds') % 60 }}s
                </span>
            </div>

            @foreach($block->segments->sortBy('order_index') as $segIndex => $segment)
            @php $segNum = "B{$blockNum}." . ($segIndex + 1); @endphp

                @if($segment->type === 'CORTE_COMERCIAL')
                    <div class="corte-comercial">
                        ── {{ $segNum }} &nbsp;·&nbsp; {{ $segment->title }} ──
                    </div>
                @else
                    <div class="segmento">
                        <div class="segmento-cabecera">
                            <span class="seg-codigo">{{ $segNum }}</span>
                            <span class="seg-titulo">{{ $segment->title }}</span>
                            <span class="seg-tipo tipo-{{ $segment->type }}">
                                {{ $typeLabels[$segment->type] ?? $segment->type }}
                            </span>
                            <span class="seg-duracion">
                                {{ floor($segment->duration_seconds / 60) }}m {{ $segment->duration_seconds % 60 }}s
                            </span>
                        </div>

                        @if($segment->script_content)
                            <div class="guion-wrapper">
                                <div class="guion">{{ $segment->script_content }}</div>
                            </div>
                        @else
                            <div class="sin-guion">— Sin guion literario —</div>
                        @endif
                    </div>
                @endif

            @endforeach

        </div>

    @endforeach

    <div class="fin">
        ★ &nbsp; Fin del Guion &nbsp;·&nbsp; {{ $rundown->show->title }} &nbsp;·&nbsp; {{ \Carbon\Carbon::parse($rundown->air_date)->format('d/m/Y') }} &nbsp; ★
    </div>

</div>

</body>
</html>