<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: letter portrait;
            margin: 2cm 2cm 2cm 2cm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #1a1a1a;
            line-height: 1.4;
            background: white;
        }

        /* ── HEADER FIJO ── */
        .page-header {
            position: fixed;
            top: -1.5cm;
            left: 0; right: 0;
            padding-bottom: 6px;
            border-bottom: 1.5px solid #cbd5e1;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .page-header .show-name {
            font-size: 8pt;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .page-header .show-date {
            font-size: 7pt;
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
            bottom: -1.5cm;
            left: 0; right: 0;
            padding-top: 5px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 6.5pt;
            color: #cbd5e1;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* ── TÍTULO PRIMERA PÁGINA ── */
        .titulo-pagina {
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 3px solid #334155;
        }
        .titulo-pagina .doc-label {
            font-size: 7.5pt;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 6px;
        }
        .titulo-pagina h1 {
            font-size: 20pt;
            font-weight: bold;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 2px;
            line-height: 1.1;
            margin-bottom: 8px;
        }
        .titulo-pagina .linea {
            width: 40px;
            height: 3px;
            background: #3b82f6;
            margin-bottom: 10px;
        }
        .titulo-pagina .meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .titulo-pagina .meta-text {
            font-size: 9pt;
            color: #64748b;
        }
        .titulo-pagina .duracion-badge {
            font-size: 8pt;
            font-weight: bold;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            padding: 3px 12px;
            letter-spacing: 0.5px;
        }

        /* ── CABECERA DE BLOQUE ── */
        .bloque-header {
            margin-top: 28px;
            margin-bottom: 12px;
            padding: 7px 12px;
            background-color: #334155;
            display: flex;
            align-items: center;
            gap: 10px;
            page-break-after: avoid;
        }
        .bloque-codigo {
            font-size: 9pt;
            font-weight: bold;
            color: #93c5fd;
            background: rgba(255,255,255,0.1);
            padding: 1px 8px;
            letter-spacing: 1px;
            font-family: 'DejaVu Sans Mono', monospace;
        }
        .bloque-titulo {
            font-size: 9pt;
            font-weight: bold;
            color: #e2e8f0;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            flex: 1;
        }
        .bloque-subtitulo {
            font-size: 8pt;
            color: #93c5fd;
            font-weight: normal;
        }
        .bloque-duracion {
            font-size: 7.5pt;
            color: #94a3b8;
            white-space: nowrap;
        }

        /* ── SEGMENTO ── */
        .segmento {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .segmento-cabecera {
            display: flex;
            align-items: baseline;
            gap: 8px;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e2e8f0;
        }
        .seg-codigo {
            font-size: 7.5pt;
            font-weight: bold;
            color: #3b82f6;
            background: #eff6ff;
            padding: 1px 7px;
            white-space: nowrap;
            font-family: 'DejaVu Sans Mono', monospace;
        }
        .seg-titulo {
            font-size: 10pt;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            flex: 1;
        }
        .seg-tipo {
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 1px 6px;
            white-space: nowrap;
        }
        .seg-duracion {
            font-size: 7pt;
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
            margin-left: 32px;
            padding-left: 14px;
            border-left: 2px solid #e2e8f0;
        }
        .guion {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 11pt;
            line-height: 1.45;
            color: #0f172a;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .sin-guion {
            font-size: 8pt;
            color: #cbd5e1;
            font-style: italic;
            margin-left: 32px;
            padding: 2px 0;
        }

        /* ── CORTE COMERCIAL ── */
        .corte-comercial {
            text-align: center;
            margin: 16px 0;
            padding: 8px 14px;
            border-top: 1px dashed #f59e0b;
            border-bottom: 1px dashed #f59e0b;
            color: #92400e;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            background: #fffbeb;
        }

        /* ── FIN ── */
        .fin {
            text-align: center;
            margin-top: 40px;
            padding: 14px;
            border-top: 1.5px solid #cbd5e1;
            color: #94a3b8;
            font-size: 7.5pt;
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

{{-- TÍTULO PRIMERA PÁGINA (sin portada) --}}
<div class="titulo-pagina">
    <div class="doc-label">Guion Literario</div>
    <h1>{{ $rundown->show->title }}</h1>
    <div class="linea"></div>
    <div class="meta-row">
        <div class="meta-text">
            {{ \Carbon\Carbon::parse($rundown->air_date)->isoFormat('dddd D [de] MMMM [de] YYYY') }}
            &nbsp;·&nbsp; Estado: {{ ucfirst($rundown->status) }}
        </div>
        <span class="duracion-badge">Duración estimada: {{ $totalMin }} min {{ $totalSeg }} seg</span>
    </div>
</div>

{{-- BLOQUES --}}
@foreach($rundown->blocks->sortBy('order_index') as $blockIndex => $block)
@php
    $blockLetra = chr(65 + $blockIndex);
    $blockNum   = $blockIndex + 1;
@endphp

    <div class="bloque-header">
        <span class="bloque-codigo">{{ $blockLetra }}</span>
        <span class="bloque-titulo">
            BLOQUE {{ $blockLetra }}
            @if($block->title)
                <span class="bloque-subtitulo">— {{ $block->title }}</span>
            @endif
        </span>
        <span class="bloque-duracion">
            {{ floor($block->segments->sum('duration_seconds') / 60) }}m
            {{ $block->segments->sum('duration_seconds') % 60 }}s
        </span>
    </div>

    @foreach($block->segments->sortBy('order_index') as $segIndex => $segment)
    @php $segNum = $blockLetra . '.' . ($segIndex + 1); @endphp

        @if($segment->type === 'CORTE_COMERCIAL')
            <div class="corte-comercial">
                ── {{ $segNum }} &nbsp;·&nbsp; {{ $segment->title }} ──
            </div>
            @if($segment->script_content)
                <div class="guion-wrapper" style="margin-bottom:14px">
                    <div class="guion">{{ $segment->script_content }}</div>
                </div>
            @endif
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

@endforeach

<div class="fin">
    ★ &nbsp; Fin del Guion &nbsp;·&nbsp; {{ $rundown->show->title }} &nbsp;·&nbsp; {{ \Carbon\Carbon::parse($rundown->air_date)->format('d/m/Y') }} &nbsp; ★
</div>

</body>
</html>
