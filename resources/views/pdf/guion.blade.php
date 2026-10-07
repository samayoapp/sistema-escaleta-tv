<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $rundown->show->title }} — @if($rundown->show->hasEpisode() && $rundown->episode_number)EP {{ $rundown->episode_number }}: @endif{{ $rundown->getEditionTitle() }} — Guion</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: letter portrait;
            margin: 2cm 2.8cm 2cm 2cm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #1a1a1a;
            line-height: 1.0;
            background: white;
        }

        /* ── HEADER FIJO ── */
        .page-header {
            position: fixed;
            top: -1.5cm;
            left: 0; right: 0;
            padding-bottom: 6px;
            border-bottom: 1.5px solid #cbd5e1;
        }
        .page-header table { width: 100%; border-collapse: collapse; }
        .page-header td    { border: none; padding: 0; vertical-align: bottom; }
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
            text-align: right;
        }

        /* ── FOOTER FIJO ── */
        .page-footer {
            position: fixed;
            bottom: -1.5cm;
            left: 0; right: 0;
            padding-top: 5px;
            border-top: 1px solid #e2e8f0;
        }
        .page-footer table { width: 100%; border-collapse: collapse; }
        .page-footer td    { border: none; padding: 0; font-size: 6.5pt; color: #94a3b8; }

        /* ── TÍTULO PRIMERA PÁGINA ── */
        .titulo-pagina {
            border-top: 4px solid #1e3a5f;
            border-bottom: 1px solid #e2e8f0;
            padding: 10px 0 8px 0;
            margin-bottom: 14px;
        }
        .titulo-top-table { width: 100%; border-collapse: collapse; }
        .titulo-top-table td { border: none; padding: 0; vertical-align: middle; }
        .titulo-show {
            font-size: 17pt;
            font-weight: bold;
            color: #1e3a5f;
            text-transform: uppercase;
            letter-spacing: 2px;
            line-height: 1.1;
        }
        .titulo-canal {
            font-size: 7.5pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 3px;
        }
        .titulo-doc-label {
            font-size: 6.5pt;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 3px;
            text-align: right;
        }
        .titulo-fecha {
            font-size: 12pt;
            font-weight: bold;
            color: #334155;
            text-align: right;
        }
        .titulo-episodio {
            font-size: 10.5pt;
            font-weight: bold;
            color: #0284c7;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
        }
        .titulo-ep-badge {
            background-color: #e0f2fe;
            color: #0369a1;
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 8.5pt;
            font-family: 'DejaVu Sans Mono', monospace;
        }
        .titulo-meta-fecha {
            font-size: 8pt;
            color: #475569;
            text-align: right;
            margin-top: 2px;
        }
        .titulo-datos {
            background: #f8fafc;
            border-left: 4px solid #1e3a5f;
            border-bottom: 2px solid #e2e8f0;
            padding: 5px 14px;
            margin-bottom: 18px;
        }
        .titulo-datos table { width: 100%; border-collapse: collapse; }
        .titulo-datos td    { border: none; padding: 2px 20px 2px 0; vertical-align: middle; }
        .dato-lbl {
            font-size: 6pt; color: #94a3b8;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .dato-val {
            font-size: 9pt; font-weight: bold;
            color: #1e3a5f; font-family: 'DejaVu Sans Mono', monospace;
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
        .seg-tipo-badge { background: #f1f5f9; color: #475569; }

        /* ── GUION LITERARIO ── */
        .guion-wrapper {
            margin-left: 32px;
            padding-left: 14px;
            padding-right: 100px;
            border-left: 2px solid #e2e8f0;
        }
        .guion {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 11pt;
            line-height: 1.3;
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

    $productionType  = $rundown->show->production_type ?? 'live';
    $segmentTypesCfg = \App\Config\SegmentTypes::forType($productionType);
    $typeLabels      = collect($segmentTypesCfg)->pluck('label', 'value')->toArray();
    $typeBorders     = collect($segmentTypesCfg)->pluck('border', 'value')->toArray();
    $isCommercialType = fn($type) => str_contains(strtolower($type), 'comercial');

@endphp

{{-- HEADER FIJO --}}
<div class="page-header">
    <table><tr>
        <td style="width:70%;">
            <div class="show-name">
                {{ $rundown->show->title }}
                @if($rundown->show->hasEpisode())
                    @if($rundown->episode_number || $rundown->episode_name)
                        <span style="color:#0284c7; font-size:7.5pt; font-weight:normal; text-transform:none;">
                            — @if($rundown->episode_number)<strong>EP {{ $rundown->episode_number }}</strong>@endif @if($rundown->episode_number && $rundown->episode_name): @endif{{ $rundown->episode_name }}
                        </span>
                    @endif
                @else
                    <span style="color:#dc2626; font-size:7.5pt; font-weight:normal; text-transform:none;">
                        — <strong>[EN VIVO]</strong> {{ $rundown->getEditionTitle() }}
                    </span>
                @endif
            </div>
            <div class="show-date">
                Aire: {{ \Carbon\Carbon::parse($rundown->air_date)->format('d/m/Y') }}
                @if($rundown->delivery_date)
                    &nbsp;|&nbsp; <span style="color:#b45309;">Entrega: {{ \Carbon\Carbon::parse($rundown->delivery_date)->format('d/m/Y') }}</span>
                @endif
            </div>
        </td>
        <td style="width:30%; text-align:right;">
            <div class="label">Guion Literario</div>
        </td>
    </tr></table>
</div>

{{-- FOOTER FIJO --}}
<div class="page-footer">
    <table><tr>
        <td>
            {{ strtoupper($rundown->show->title) }}
            @if($rundown->show->hasEpisode())
                @if($rundown->episode_number || $rundown->episode_name)
                    &nbsp;·&nbsp; @if($rundown->episode_number)EP {{ $rundown->episode_number }}@endif @if($rundown->episode_number && $rundown->episode_name): @endif{{ $rundown->episode_name }}
                @endif
            @else
                &nbsp;·&nbsp; EN VIVO: {{ $rundown->getEditionTitle() }}
            @endif
            &nbsp;·&nbsp; Aire: {{ \Carbon\Carbon::parse($rundown->air_date)->format('d/m/Y') }}
            @if($rundown->delivery_date)
                &nbsp;·&nbsp; Entrega: {{ \Carbon\Carbon::parse($rundown->delivery_date)->format('d/m/Y') }}
            @endif
        </td>
        <td style="text-align:right;">USO INTERNO — GUION</td>
    </tr></table>
</div>

{{-- TÍTULO PRIMERA PÁGINA --}}
<div class="titulo-pagina">
    <table class="titulo-top-table"><tr>
        <td style="width:62%">
            <div class="titulo-show">{{ $rundown->show->title }}</div>
            @if($rundown->show->hasEpisode())
                @if($rundown->episode_number || $rundown->episode_name)
                    <div class="titulo-episodio">
                        @if($rundown->episode_number)
                            <span class="titulo-ep-badge">EP {{ $rundown->episode_number }}</span>
                        @endif
                        @if($rundown->episode_name)
                            <span>{{ $rundown->episode_name }}</span>
                        @endif
                    </div>
                @endif
            @else
                <div class="titulo-episodio" style="color:#b91c1c;">
                    <span class="titulo-ep-badge" style="background-color:#fee2e2; color:#b91c1c;">EN VIVO</span>
                    <span style="color:#1e293b;">{{ $rundown->getEditionTitle() }}</span>
                </div>
            @endif
            @if($rundown->show->channel)
                <div class="titulo-canal">{{ $rundown->show->channel }}</div>
            @endif
        </td>
        <td style="width:38%; text-align:right;">
            <div class="titulo-doc-label">Guion Literario</div>
            <div class="titulo-meta-fecha">
                <span style="color:#64748b; font-size:7pt; text-transform:uppercase;">Fecha de Aire:</span>
                <span style="font-size:10pt; font-weight:bold; color:#1e3a5f;">{{ \Carbon\Carbon::parse($rundown->air_date)->format('d/m/Y') }}</span>
            </div>
            @if($rundown->delivery_date)
            <div class="titulo-meta-fecha" style="margin-top:2px;">
                <span style="color:#92400e; font-size:7pt; text-transform:uppercase;">Fecha de Entrega:</span>
                <span style="font-size:10pt; font-weight:bold; color:#b45309;">{{ \Carbon\Carbon::parse($rundown->delivery_date)->format('d/m/Y') }}</span>
            </div>
            @endif
        </td>
    </tr></table>
</div>
<div class="titulo-datos">
    <table><tr>
        <td>
            <div class="dato-lbl">Fecha de Aire</div>
            <div class="dato-val">{{ \Carbon\Carbon::parse($rundown->air_date)->format('d/m/Y') }}</div>
        </td>
        @if($rundown->delivery_date)
        <td>
            <div class="dato-lbl" style="color:#b45309;">Fecha Entrega</div>
            <div class="dato-val" style="color:#b45309;">{{ \Carbon\Carbon::parse($rundown->delivery_date)->format('d/m/Y') }}</div>
        </td>
        @endif
        @if($rundown->show->hasAirTime())
        <td>
            <div class="dato-lbl">Hora de Inicio</div>
            <div class="dato-val">{{ substr($rundown->air_time ?? '00:00:00', 0, 5) }}</div>
        </td>
        @endif
        <td>
            <div class="dato-lbl">Estado</div>
            <div class="dato-val">{{ ucfirst($rundown->status) }}</div>
        </td>
        <td>
            <div class="dato-lbl">Duración estimada</div>
            <div class="dato-val">{{ $totalMin }}m {{ $totalSeg }}s</div>
        </td>
        <td>
            <div class="dato-lbl">Ítems con guion</div>
            <div class="dato-val">{{ $rundown->blocks->flatMap->segments->where('has_script', true)->count() }}</div>
        </td>
    </tr></table>
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

        @if($isCommercialType($segment->type))
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
                    @php $borderColor = $typeBorders[$segment->type] ?? '#94a3b8'; @endphp
                    <span class="seg-tipo" style="background:{{ $borderColor }}22; color:{{ $borderColor }}; border:1px solid {{ $borderColor }}44">
                        {{ $typeLabels[$segment->type] ?? $segment->type }}
                    </span>
                    <span class="seg-titulo">{{ $segment->title }}</span>
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
    ★ &nbsp; Fin del Guion &nbsp;·&nbsp; {{ $rundown->show->title }}
    @if($rundown->show->hasEpisode())
        @if($rundown->episode_number || $rundown->episode_name)
            &nbsp;·&nbsp; @if($rundown->episode_number)EP {{ $rundown->episode_number }}@endif @if($rundown->episode_number && $rundown->episode_name): @endif{{ $rundown->episode_name }}
        @endif
    @else
        &nbsp;·&nbsp; EN VIVO: {{ $rundown->getEditionTitle() }}
    @endif
    &nbsp;·&nbsp; Aire: {{ \Carbon\Carbon::parse($rundown->air_date)->format('d/m/Y') }}
    @if($rundown->delivery_date)
        &nbsp;·&nbsp; Entrega: {{ \Carbon\Carbon::parse($rundown->delivery_date)->format('d/m/Y') }}
    @endif
    &nbsp; ★
</div>

</body>
</html>
