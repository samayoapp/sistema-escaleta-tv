<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: letter landscape;
            margin: 1.8cm 2cm 2cm 2cm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            color: #1a1a1a;
            background: white;
        }

        /* ── HEADER FIJO ── */
        .page-header {
            position: fixed;
            top: -1.4cm;
            left: 0; right: 0;
            padding-bottom: 6px;
            border-bottom: 2px solid #1e3a5f;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .page-header .show-name {
            font-size: 10pt;
            font-weight: bold;
            color: #1e3a5f;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .page-header .meta {
            font-size: 7.5pt;
            color: #64748b;
            margin-top: 2px;
        }
        .page-header .right {
            text-align: right;
        }
        .page-header .label {
            font-size: 8pt;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ── FOOTER FIJO ── */
        .page-footer {
            position: fixed;
            bottom: -1.5cm;
            left: 0; right: 0;
            padding-top: 5px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            font-size: 7pt;
            color: #94a3b8;
            letter-spacing: 0.5px;
        }

        /* ── PORTADA ── */
        .portada {
            text-align: center;
            padding: 50px 20px 40px;
            page-break-after: always;
        }
        .portada .label {
            font-size: 8pt;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 4px;
            margin-bottom: 16px;
        }
        .portada h1 {
            font-size: 24pt;
            font-weight: bold;
            color: #1e3a5f;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 16px;
        }
        .portada .linea {
            width: 60px;
            height: 3px;
            background: #3b82f6;
            margin: 0 auto 20px;
        }
        .portada .meta {
            font-size: 10pt;
            color: #64748b;
            margin-bottom: 6px;
        }
        .portada .badge {
            display: inline-block;
            margin-top: 20px;
            border: 1.5px solid #bfdbfe;
            color: #3b82f6;
            font-size: 10pt;
            font-weight: bold;
            padding: 6px 20px;
            letter-spacing: 1px;
        }

        /* ── TABLA PRINCIPAL ── */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        /* Anchos de columnas */
        .col-codigo   { width: 7%; }
        .col-titulo   { width: 38%; }
        .col-tipo     { width: 13%; }
        .col-duracion { width: 12%; }
        .col-horaaire { width: 14%; }
        .col-notas    { width: 16%; }

        /* ── CABECERA DE TABLA ── */
        thead tr th {
            background-color: #1e3a5f;
            color: white;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 7px 8px;
            text-align: left;
            border: 1px solid #162d47;
        }
        thead tr th.text-center { text-align: center; }

        /* ── FILA SEPARADORA DE BLOQUE ── */
        .fila-bloque td {
            background-color: #334155;
            color: white;
            font-size: 8.5pt;
            font-weight: bold;
            padding: 6px 10px;
            border: 1px solid #1e293b;
        }
        .fila-bloque .bloque-codigo {
            font-family: 'DejaVu Sans Mono', monospace;
            color: #93c5fd;
            background: rgba(255,255,255,0.08);
            padding: 1px 7px;
            font-size: 8pt;
        }
        .fila-bloque .bloque-titulo {
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .fila-bloque .bloque-duracion {
            text-align: right;
            color: #93c5fd;
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 8pt;
        }
        .fila-bloque .bloque-hora {
            text-align: center;
            color: #fcd34d;
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 8pt;
        }

        /* ── FILAS DE SEGMENTO ── */
        tbody tr.seg-row td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
            font-size: 8.5pt;
        }
        tbody tr.seg-row:nth-child(even) td {
            background-color: #f8fafc;
        }
        tbody tr.seg-row:nth-child(odd) td {
            background-color: #ffffff;
        }

        /* Colores borde izquierdo por tipo */
        .seg-VIVO            td:first-child { border-left: 4px solid #ef4444; }
        .seg-VTR             td:first-child { border-left: 4px solid #22c55e; }
        .seg-OFF             td:first-child { border-left: 4px solid #a855f7; }
        .seg-CORTE_COMERCIAL td:first-child { border-left: 4px solid #eab308; }
        .seg-NOTA_SECA       td:first-child { border-left: 4px solid #94a3b8; }
        .seg-PRESENTACION    td:first-child { border-left: 4px solid #3b82f6; }
        .seg-CIERRE          td:first-child { border-left: 4px solid #f97316; }

        .td-codigo {
            font-family: 'DejaVu Sans Mono', monospace;
            font-weight: bold;
            color: #3b82f6;
            font-size: 8pt;
            text-align: center;
        }
        .td-titulo {
            font-weight: bold;
            color: #1e293b;
        }
        .td-tipo {
            text-align: center;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 3px 5px;
            border-radius: 2px;
        }
        .td-duracion {
            font-family: 'DejaVu Sans Mono', monospace;
            text-align: center;
            color: #475569;
            font-size: 8.5pt;
        }
        .td-hora {
            font-family: 'DejaVu Sans Mono', monospace;
            text-align: center;
            font-weight: bold;
            color: #b45309;
            font-size: 8.5pt;
            background: #fffbeb !important;
        }
        .td-notas {
            color: #94a3b8;
            font-size: 7.5pt;
            font-style: italic;
        }

        /* Badges de tipo */
        .badge-VIVO            { background: #fee2e2; color: #b91c1c; }
        .badge-VTR             { background: #dcfce7; color: #15803d; }
        .badge-OFF             { background: #f3e8ff; color: #7e22ce; }
        .badge-CORTE_COMERCIAL { background: #fef9c3; color: #854d0e; }
        .badge-NOTA_SECA       { background: #f1f5f9; color: #475569; }
        .badge-PRESENTACION    { background: #dbeafe; color: #1d4ed8; }
        .badge-CIERRE          { background: #ffedd5; color: #c2410c; }

        /* ── FILA CORTE COMERCIAL ESPECIAL ── */
        .fila-comercial td {
            background: #fffbeb !important;
            border-top: 1px dashed #f59e0b !important;
            border-bottom: 1px dashed #f59e0b !important;
            color: #92400e;
            font-weight: bold;
            text-align: center;
            font-size: 8pt;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        /* ── FILA TOTALES ── */
        .fila-total td {
            background: #f1f5f9;
            border-top: 2px solid #334155;
            padding: 7px 8px;
            font-weight: bold;
            font-size: 8.5pt;
            color: #1e293b;
        }
        .fila-total .total-label {
            text-align: right;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
        }
        .fila-total .total-valor {
            font-family: 'DejaVu Sans Mono', monospace;
            text-align: center;
            color: #1e3a5f;
        }
        .fila-total .total-fin {
            font-family: 'DejaVu Sans Mono', monospace;
            text-align: center;
            color: #b45309;
            background: #fffbeb;
        }
    </style>
</head>
<body>

@php
    // Calcular tiempo acumulado
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
    <div>
        <div class="show-name">{{ $rundown->show->title }}</div>
        <div class="meta">
            Emisión: {{ \Carbon\Carbon::parse($rundown->air_date)->format('d/m/Y') }}
            &nbsp;·&nbsp;
            Inicio: {{ escFmtHora($acumulado) }}
        </div>
    </div>
    <div class="right">
        <div class="label">Escaleta de Producción</div>
        <div class="meta">Duración total: {{ $totalMin }}m {{ $totalSeg }}s &nbsp;·&nbsp; Fin estimado: {{ escFmtHora($horaFin) }}</div>
    </div>
</div>

{{-- FOOTER FIJO --}}
<div class="page-footer">
    <span>{{ strtoupper($rundown->show->title) }} · {{ \Carbon\Carbon::parse($rundown->air_date)->format('d/m/Y') }}</span>
    <span>USO INTERNO — PRODUCCIÓN</span>
</div>

{{-- PORTADA --}}
<div class="portada">
    <div class="label">Escaleta de Producción</div>
    <h1>{{ $rundown->show->title }}</h1>
    <div class="linea"></div>
    <div class="meta">Fecha de emisión: {{ \Carbon\Carbon::parse($rundown->air_date)->format('d \d\e F \d\e Y') }}</div>
    <div class="meta">Hora de inicio: {{ escFmtHora($acumulado) }} &nbsp;·&nbsp; Fin estimado: {{ escFmtHora($horaFin) }}</div>
    <div class="badge">Duración total: {{ $totalMin }} min {{ $totalSeg }} seg</div>
</div>

{{-- TABLA --}}
<table>
    <thead>
        <tr>
            <th class="col-codigo text-center">Código</th>
            <th class="col-titulo">Título del Ítem</th>
            <th class="col-tipo text-center">Tipo</th>
            <th class="col-duracion text-center">Duración</th>
            <th class="col-horaaire text-center">⏱ Al Aire</th>
            <th class="col-notas">Notas</th>
        </tr>
    </thead>
    <tbody>

        @foreach($rundown->blocks->sortBy('order_index') as $blockIndex => $block)
        @php
            $blockNum   = $blockIndex + 1;
            $blockStart = $acumulado;
        @endphp

            {{-- FILA SEPARADORA DE BLOQUE --}}
            <tr class="fila-bloque">
                <td class="text-center">
                    <span class="bloque-codigo">B{{ $blockNum }}</span>
                </td>
                <td colspan="3" class="bloque-titulo">{{ $block->title }}</td>
                <td class="bloque-hora">▶ {{ escFmtHora($blockStart) }}</td>
                <td class="bloque-duracion">{{ escFmtDur($block->segments->sum('duration_seconds')) }}</td>
            </tr>

            {{-- SEGMENTOS --}}
            @foreach($block->segments->sortBy('order_index') as $segIndex => $segment)
            @php
                $segNum  = "B{$blockNum}." . ($segIndex + 1);
                $horaFinSeg = $acumulado + $segment->duration_seconds;
                $acumulado += $segment->duration_seconds;
            @endphp

                @if($segment->type === 'CORTE_COMERCIAL')
                    <tr class="fila-comercial">
                        <td>{{ $segNum }}</td>
                        <td colspan="3">── {{ $segment->title }} ──</td>
                        <td>{{ escFmtHora($horaFinSeg) }}</td>
                        <td>{{ escFmtDur($segment->duration_seconds) }}</td>
                    </tr>
                @else
                    <tr class="seg-row seg-{{ $segment->type }}">
                        <td class="td-codigo">{{ $segNum }}</td>
                        <td class="td-titulo">{{ $segment->title }}</td>
                        <td class="td-tipo">
                            <span class="td-tipo badge-{{ $segment->type }}">
                                {{ $typeLabels[$segment->type] ?? $segment->type }}
                            </span>
                        </td>
                        <td class="td-duracion">{{ escFmtDur($segment->duration_seconds) }}</td>
                        <td class="td-hora">{{ escFmtHora($horaFinSeg) }}</td>
                        <td class="td-notas"></td>
                    </tr>
                @endif

            @endforeach

        @endforeach

        {{-- FILA TOTALES --}}
        <tr class="fila-total">
            <td colspan="3" class="total-label">Duración Total del Programa</td>
            <td class="total-valor">{{ escFmtDur($totalSeconds) }}</td>
            <td class="total-fin">{{ escFmtHora($horaFin) }}</td>
            <td></td>
        </tr>

    </tbody>
</table>

</body>
</html>
