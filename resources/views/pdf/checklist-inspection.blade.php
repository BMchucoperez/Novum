<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Inspección - {{ $inspection->vessel->name }}</title>
    <style>
        @page {
            size: A4;
            margin: 2cm; /* Margen de la página */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #333333;
            padding: 0 2cm; /* Padding interno adicional izquierda/derecha */
        }
        
        .header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333333;
        }

        .header h1 {
            color: #333333;
            font-size: 18pt;
            margin-bottom: 5px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .header p {
            color: #666666;
            font-size: 9pt;
            font-weight: 300;
        }
        
        .info-section {
            background-color: #f9f9f9;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 3px solid #333333;
        }
        
        .info-grid {
            display: table;
            width: 100%;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: 500;
            color: #555555;
            padding: 5px 10px 5px 0;
            width: 35%;
            font-size: 9pt;
        }

        .info-value {
            display: table-cell;
            color: #333333;
            padding: 5px 0;
            font-size: 9pt;
            font-weight: 400;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            font-weight: 500;
            font-size: 8.5pt;
        }

        .status-apto {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .status-no-apto {
            background-color: #ffebee;
            color: #c62828;
        }

        .status-observado {
            background-color: #fff9c4;
            color: #f57f17;
        }
        
        .parte-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .parte-title {
            background-color: #333333;
            color: white;
            padding: 10px 15px;
            font-size: 11pt;
            font-weight: 500;
            margin-bottom: 10px;
            letter-spacing: 0.3px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            margin-left: 0;
            margin-right: 0;
        }

        .items-table th {
            background-color: #666666;
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-size: 9pt;
            font-weight: 500;
            border: none;
            letter-spacing: 0.3px;
        }

        .items-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #dddddd;
            font-size: 9pt;
            vertical-align: top;
        }

        .items-table tr:nth-child(even) {
            background-color: #f5f5f5;
        }
        
        .estado-badge {
            display: inline-block;
            padding: 3px 8px;
            font-weight: 500;
            font-size: 8pt;
        }

        .estado-A {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .estado-N {
            background-color: #ffebee;
            color: #c62828;
        }

        .estado-O {
            background-color: #fff9c4;
            color: #f57f17;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #cccccc;
            text-align: center;
            font-size: 8pt;
            color: #888888;
            line-height: 1.6;
        }

        .footer p {
            margin: 2px 0;
        }

        .page-break {
            page-break-after: always;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
        }
        
        .summary-box {
            background-color: #f9f9f9;
            border: 1px solid #cccccc;
            padding: 15px;
            margin-bottom: 20px;
        }

        .summary-title {
            font-size: 10.5pt;
            font-weight: 500;
            color: #333333;
            margin-bottom: 10px;
            letter-spacing: 0.3px;
        }
        
        .summary-stats {
            display: table;
            width: 100%;
        }
        
        .summary-stat {
            display: table-cell;
            text-align: center;
            padding: 8px;
        }
        
        .summary-stat-value {
            font-size: 14pt;
            font-weight: 500;
            display: block;
        }

        .summary-stat-label {
            font-size: 8pt;
            color: #888888;
            display: block;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
    <!-- Header -->
    <div class="header">
        <h1>Reporte de Inspección</h1>
        <p>Sistema de Gestión de Inspecciones Marítimas</p>
    </div>

    <!-- Información General -->
    <div class="info-section">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Embarcación:</div>
                <div class="info-value">{{ $inspection->vessel->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Propietario:</div>
                <div class="info-value">{{ $inspection->owner->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha Inicio:</div>
                <div class="info-value">{{ $inspection->inspection_start_date->format('d/m/Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha Fin:</div>
                <div class="info-value">{{ $inspection->inspection_end_date->format('d/m/Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Inspector:</div>
                <div class="info-value">{{ $inspection->inspector_name ?? 'No asignado' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Estado General:</div>
                <div class="info-value">
                    <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $inspection->overall_status)) }}">
                        {{ $inspection->overall_status }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen Estadístico -->
    <div class="summary-box">
        <div class="summary-title">Resumen de Evaluación</div>
        <div class="summary-stats">
            <div class="summary-stat">
                <span class="summary-stat-value" style="color: #2e7d32;">{{ $stats['apto'] }}</span>
                <span class="summary-stat-label">APTO</span>
            </div>
            <div class="summary-stat">
                <span class="summary-stat-value" style="color: #c62828;">{{ $stats['no_apto'] }}</span>
                <span class="summary-stat-label">NO APTO</span>
            </div>
            <div class="summary-stat">
                <span class="summary-stat-value" style="color: #f57f17;">{{ $stats['observado'] }}</span>
                <span class="summary-stat-label">OBSERVADO</span>
            </div>
            <div class="summary-stat">
                <span class="summary-stat-value" style="color: #666666;">{{ $stats['total'] }}</span>
                <span class="summary-stat-label">TOTAL</span>
            </div>
        </div>
    </div>

    <!-- Partes del Checklist -->
    @foreach($partes as $parteNum => $parteData)
        @if(!empty($parteData['items']))
            <div class="parte-section">
                <div class="parte-title">{{ $parteData['title'] }}</div>
                
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 8%;">#</th>
                            <th style="width: 72%;">Ítem</th>
                            <th style="width: 20%; text-align: center;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($parteData['items'] as $index => $item)
                            <tr>
                                <td style="font-weight: 500; color: #666666;">{{ $index + 1 }}</td>
                                <td>
                                    @if(!empty($item['item_es']))
                                        <div style="line-height: 1.4;">
                                            <span style="color: #333333; font-weight: 400;">{{ $item['item'] }}</span>
                                            <br>
                                            <span style="color: #888888; font-size: 8.5pt; font-weight: 300; font-style: italic;">{{ $item['item_es'] }}</span>
                                        </div>
                                    @else
                                        <span style="color: #333333; font-weight: 400;">{{ $item['item'] }}</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($item['estado'] == 'A')
                                        <span class="estado-badge estado-A">APTO</span>
                                    @elseif($item['estado'] == 'N')
                                        <span class="estado-badge estado-N">NO APTO</span>
                                    @elseif($item['estado'] == 'O')
                                        <span class="estado-badge estado-O">OBSERVADO</span>
                                    @else
                                        <span style="color: #aaaaaa; font-size: 8pt;">—</span>
                                    @endif
                                </td>
                            </tr>
                            @if(!empty($item['comentarios']))
                                <tr>
                                    <td colspan="3" style="background-color: #fffbf0; padding: 8px 10px; border-left: 2px solid #f39c12;">
                                        <strong style="color: #e67e22; font-size: 8.5pt;">Comentarios:</strong>
                                        <span style="color: #666666; font-size: 8.5pt;">{{ $item['comentarios'] }}</span>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endforeach

    <!-- Observaciones Generales -->
    @if($inspection->general_observations)
        <div class="info-section" style="border-left-color: #f39c12; background-color: #fffbf0;">
            <strong style="color: #333333; font-size: 9.5pt;">Observaciones Generales:</strong>
            <p style="margin-top: 8px; color: #666666; line-height: 1.5;">{{ $inspection->general_observations }}</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Reporte generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Sistema de Gestión de Inspecciones Marítimas - Navio</p>
    </div>
    </div> <!-- Cierre del container -->
</body>
</html>

