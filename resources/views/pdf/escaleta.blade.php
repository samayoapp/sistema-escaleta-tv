<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: 11in 8.5in;
            margin-top: 2.2cm;
            margin-bottom: 1.8cm;
            margin-left: 2cm;
            margin-right: 2cm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            color: #1a1a1a;
            background: white;
        }

        /* ══ HEADER FIJO ══ */
        .page-header {
            position: fixed;
            top: -1.8cm;
            left: 0; right: 0;
            border-bottom: 2px solid #1e3a5f;
            padding-bottom: 5px;
        }
        .page-header table { width: 100%; border-collapse: collapse; }
        .page-header td    { border: none; padding: 0; vertical-align: bottom; }
        .hdr-show {
            font-size: 9.5pt; font-weight: bold;
            color: #1e3a5f; text-transform: uppercase; letter-spacing: 2px;
        }
        .hdr-meta { font-size: 7pt; color: #64748b; margin-top: 2px; }
        .hdr-label {
            font-size: 7.5pt; font-weight: bold; color: #64748b;
            text-transform: uppercase; letter-spacing: 1px; text-align: right;
        }
        .hdr-sub { font-size: 7pt; color: #94a3b8; text-align: right; margin-top: 2px; }

        /* ══ FOOTER FIJO ══ */
        .page-footer {
            position: fixed;
            bottom: -1.4cm;
            left: 0; right: 0;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
        .page-footer table { width: 100%; border-collapse: collapse; }
        .page-footer td    { border: none; padding: 0; font-size: 6.5pt; color: #94a3b8; }
        .ft-right { text-align: right; }

        /* ══ ENCABEZADO PRIMERA PÁGINA — fondo blanco ══ */
        .enc-top {
            border-top: 4px solid #1e3a5f;
            border-bottom: 1px solid #e2e8f0;
            padding: 10px 0 8px 0;
            margin-bottom: 0;
        }
        .enc-top table { width: 100%; border-collapse: collapse; }
        .enc-top td    { border: none; padding: 0; vertical-align: middle; }
        .enc-show {
            font-size: 17pt; font-weight: bold; color: #1e3a5f;
            text-transform: uppercase; letter-spacing: 2px; line-height: 1.1;
        }
        .enc-canal {
            font-size: 7.5pt; color: #64748b;
            text-transform: uppercase; letter-spacing: 2px; margin-top: 3px;
        }
        .enc-doc-label {
            font-size: 6.5pt; color: #94a3b8;
            text-transform: uppercase; letter-spacing: 2px;
            margin-bottom: 3px; text-align: right;
        }
        .enc-fecha {
            font-size: 12pt; font-weight: bold;
            color: #334155; text-align: right;
        }

        .enc-datos {
            background: #f8fafc;
            border-left: 4px solid #1e3a5f;
            border-bottom: 2px solid #e2e8f0;
            padding: 5px 14px;
            margin-bottom: 14px;
        }
        .enc-datos table { width: 100%; border-collapse: collapse; }
        .enc-datos td    { border: none; padding: 2px 20px 2px 0; vertical-align: middle; }
        .dato-lbl {
            font-size: 6pt; color: #94a3b8;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .dato-val {
            font-size: 9pt; font-weight: bold;
            color: #1e3a5f; font-family: 'DejaVu Sans Mono', monospace;
        }

        /* ══ TABLA PRINCIPAL ══ */
        .tbl { width: 100%; border-collapse: collapse; table-layout: fixed; }

        /* TIPO va primero, luego título */
        .col-cod  { width: 6%; }
        .col-tipo { width: 11%; }
        .col-tit  { width: 37%; }
        .col-dur  { width: 10%; }
        .col-aire { width: 12%; }
        .col-not  { width: 24%; }

        .tbl thead th {
            background: #1e3a5f; color: #e2e8f0;
            font-size: 7pt; font-weight: bold;
            text-transform: uppercase; letter-spacing: 1px;
            padding: 6px 8px; text-align: left;
            border: 1px solid #162d47;
        }
        .tbl thead th.tc { text-align: center; }

        /* Fila bloque */
        .fila-blq td {
            background: #334155; color: #e2e8f0;
            font-size: 8pt; font-weight: bold;
            padding: 6px 8px; border: 1px solid #1e293b;
            vertical-align: middle;
        }
        .blq-letra {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 10pt; font-weight: bold;
            color: #fff; background: #1e3a5f;
            padding: 1px 8px;
        }
        .blq-nom  { text-transform: uppercase; letter-spacing: 1.5px; }
        .blq-sub  { color: #93c5fd; font-weight: normal; font-size: 7.5pt; }
        .blq-hora { text-align: center; color: #fcd34d; font-family: 'DejaVu Sans Mono', monospace; }
        .blq-dur  { text-align: right;  color: #93c5fd; font-family: 'DejaVu Sans Mono', monospace; }

        /* Filas segmento */
        .seg-r td {
            padding: 5px 8px; border: 1px solid #e2e8f0;
            vertical-align: middle; font-size: 8.5pt;
        }
        .seg-r.par   td { background: #f8fafc; }
        .seg-r.impar td { background: #ffffff; }

        /* Borde izquierdo — ahora en primera celda (código) */
        .seg-VIVO            td:first-child { border-left: 4px solid #ef4444; }
        .seg-VTR             td:first-child { border-left: 4px solid #22c55e; }
        .seg-OFF             td:first-child { border-left: 4px solid #a855f7; }
        .seg-CORTE_COMERCIAL td:first-child { border-left: 4px solid #eab308; }
        .seg-NOTA_SECA       td:first-child { border-left: 4px solid #94a3b8; }
        .seg-PRESENTACION    td:first-child { border-left: 4px solid #3b82f6; }
        .seg-CIERRE          td:first-child { border-left: 4px solid #f97316; }

        .td-cod {
            font-family: 'DejaVu Sans Mono', monospace;
            font-weight: bold; color: #3b82f6; font-size: 8pt; text-align: center;
        }
        .td-tipo { text-align: center; }
        .td-tit  { font-weight: bold; color: #1e293b; }
        .td-dur  { font-family: 'DejaVu Sans Mono', monospace; text-align: center; color: #475569; }
        .td-hora {
            font-family: 'DejaVu Sans Mono', monospace;
            text-align: center; font-weight: bold;
            color: #b45309; background: #fffbeb !important;
        }
        .td-not  { color: #94a3b8; font-size: 7.5pt; font-style: italic; }

        .badge {
            font-size: 7pt; font-weight: bold;
            text-transform: uppercase; padding: 2px 5px;
        }
        .badge-VIVO            { background:#fee2e2; color:#b91c1c; }
        .badge-VTR             { background:#dcfce7; color:#15803d; }
        .badge-OFF             { background:#f3e8ff; color:#7e22ce; }
        .badge-CORTE_COMERCIAL { background:#fef9c3; color:#854d0e; }
        .badge-NOTA_SECA       { background:#f1f5f9; color:#475569; }
        .badge-PRESENTACION    { background:#dbeafe; color:#1d4ed8; }
        .badge-CIERRE          { background:#ffedd5; color:#c2410c; }

        .fila-com td {
            background: #fffbeb !important;
            border-top: 1px dashed #f59e0b !important;
            border-bottom: 1px dashed #f59e0b !important;
            color: #92400e; font-weight: bold;
            text-align: center; font-size: 8pt;
            letter-spacing: 1.5px; text-transform: uppercase;
        }

        .fila-tot td {
            background: #f1f5f9;
            border-top: 2px solid #334155;
            padding: 7px 8px; font-weight: bold; color: #1e293b;
        }
        .tot-lbl  { text-align: right; color: #64748b; font-size: 7.5pt; text-transform: uppercase; letter-spacing: 1px; }
        .tot-val  { font-family: 'DejaVu Sans Mono', monospace; text-align: center; color: #1e3a5f; font-size: 10pt; }
        .tot-fin  { font-family: 'DejaVu Sans Mono', monospace; text-align: center; color: #b45309; font-size: 10pt; background: #fffbeb; }
    </style>
</head>
<body>

@php
    $airTimeParts = explode(':', $rundown->air_time ?? '19:00:00');
    $acumulado    = ((int)$airTimeParts[0] * 3600) + ((int)$airTimeParts[1] * 60) + ((int)($airTimeParts[2] ?? 0));
    $totalSeconds = $rundown->blocks->flatMap->segments->sum('duration_seconds');
    $totalMin     = floor($totalSeconds / 60);
    $totalSeg     = $totalSeconds % 60;
    $horaFin      = $acumulado + $totalSeconds;

    function escFmtDur($s) {
        return sprintf('%02d:%02d', floor($s / 60), $s % 60);
    }
    function escFmtHora($s) {
        $s = $s % 86400;
        return sprintf('%02d:%02d:%02d', floor($s / 3600), floor(($s % 3600) / 60), $s % 60);
    }

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
    <table><tr>
        <td style="width:60%">
            <div class="hdr-show">{{ $rundown->show->title }}</div>
            <div class="hdr-meta">
                Emisión: {{ \Carbon\Carbon::parse($rundown->air_date)->format('d/m/Y') }}
                &nbsp;|&nbsp; Inicio: {{ escFmtHora($acumulado) }}
                &nbsp;|&nbsp; Fin est.: {{ escFmtHora($horaFin) }}
            </div>
        </td>
        <td style="width:40%">
            <div class="hdr-label">Escaleta de Producción</div>
            <div class="hdr-sub">Duración total: {{ $totalMin }}m {{ $totalSeg }}s</div>
        </td>
    </tr></table>
</div>

{{-- FOOTER FIJO --}}
<div class="page-footer">
    <table><tr>
        <td>{{ strtoupper($rundown->show->title) }} &nbsp;·&nbsp; {{ \Carbon\Carbon::parse($rundown->air_date)->format('d/m/Y') }}</td>
        <td class="ft-right">USO INTERNO — PRODUCCIÓN</td>
    </tr></table>
</div>

{{-- ENCABEZADO PRIMERA PÁGINA --}}
<div class="enc-top">
    <table><tr>
        <td style="width:65%">
            <div class="enc-show">{{ $rundown->show->title }}</div>
            @if($rundown->show->channel)
                <div class="enc-canal">{{ $rundown->show->channel }}</div>
            @endif
        </td>
        <td style="width:35%">
            <div class="enc-doc-label">Escaleta de Producción</div>
            <div class="enc-fecha">{{ \Carbon\Carbon::parse($rundown->air_date)->format('d/m/Y') }}</div>
        </td>
    </tr></table>
</div>
<div class="enc-datos">
    <table><tr>
        <td>
            <div class="dato-lbl">Hora de Inicio</div>
            <div class="dato-val">{{ escFmtHora($acumulado) }}</div>
        </td>
        <td>
            <div class="dato-lbl">Fin Estimado</div>
            <div class="dato-val">{{ escFmtHora($horaFin) }}</div>
        </td>
        <td>
            <div class="dato-lbl">Duración Total</div>
            <div class="dato-val">{{ $totalMin }}m {{ $totalSeg }}s</div>
        </td>
        <td>
            <div class="dato-lbl">Bloques</div>
            <div class="dato-val">{{ $rundown->blocks->count() }}</div>
        </td>
        <td>
            <div class="dato-lbl">Ítems</div>
            <div class="dato-val">{{ $rundown->blocks->flatMap->segments->count() }}</div>
        </td>
    </tr></table>
</div>

{{-- TABLA — orden columnas: Código | Tipo | Título | Duración | Al Aire | Notas --}}
<table class="tbl">
    <thead>
        <tr>
            <th class="col-cod  tc">Código</th>
            <th class="col-tipo tc">Tipo</th>
            <th class="col-tit"    >Título del Ítem</th>
            <th class="col-dur  tc">Duración</th>
            <th class="col-aire tc">Al Aire</th>
            <th class="col-not"    >Notas</th>
        </tr>
    </thead>
    <tbody>

    @php $globalRow = 0; @endphp

    @foreach($rundown->blocks->sortBy('order_index') as $blockIndex => $block)
    @php
        $blockLetra = chr(65 + $blockIndex);
        $blockStart = $acumulado;
    @endphp

        <tr class="fila-blq">
            <td style="text-align:center"><span class="blq-letra">{{ $blockLetra }}</span></td>
            <td colspan="4">
                <span class="blq-nom">BLOQUE {{ $blockLetra }}</span>
                @if($block->title)
                    <span class="blq-sub">&nbsp;&nbsp;&#8212;&nbsp;&nbsp;{{ $block->title }}</span>
                @endif
            </td>
            <td class="blq-hora">&#9654; {{ escFmtHora($blockStart) }}</td>
            {{-- nota: quitamos blq-dur de la última col porque la tabla tiene 6 cols ahora --}}
        </tr>

        @foreach($block->segments->sortBy('order_index') as $segIndex => $segment)
        @php
            $segNum     = $blockLetra . '.' . ($segIndex + 1);
            $horaFinSeg = $acumulado + $segment->duration_seconds;
            $acumulado += $segment->duration_seconds;
            $globalRow++;
            $rowClass   = ($globalRow % 2 === 0) ? 'par' : 'impar';
        @endphp

            @if($segment->type === 'CORTE_COMERCIAL')
            <tr class="fila-com">
                <td>{{ $segNum }}</td>
                <td>COM.</td>
                <td colspan="2">&#8212;&#8212; {{ $segment->title }} &#8212;&#8212;</td>
                <td>{{ escFmtHora($horaFinSeg) }}</td>
                <td class="td-not" style="text-align:left; font-style:italic; color:#92400e;">{!! \App\Http\Controllers\RundownController::linkify($segment->production_notes) !!}</td>
            </tr>
            @else
            <tr class="seg-r {{ $rowClass }} seg-{{ $segment->type }}">
                <td class="td-cod">{{ $segNum }}</td>
                <td class="td-tipo">
                    <span class="badge badge-{{ $segment->type }}">
                        {{ $typeLabels[$segment->type] ?? $segment->type }}
                    </span>
                </td>
                <td class="td-tit">{{ $segment->title }}</td>
                <td class="td-dur">{{ escFmtDur($segment->duration_seconds) }}</td>
                <td class="td-hora">{{ escFmtHora($horaFinSeg) }}</td>
                <td class="td-not">{!! \App\Http\Controllers\RundownController::linkify($segment->production_notes) !!}</td>
            </tr>
            @endif

        @endforeach
    @endforeach

    <tr class="fila-tot">
        <td colspan="3" class="tot-lbl">Duración Total del Programa</td>
        <td class="tot-val">{{ escFmtDur($totalSeconds) }}</td>
        <td class="tot-fin">{{ escFmtHora($horaFin) }}</td>
        <td></td>
    </tr>

    </tbody>
</table>

</body>
</html>
