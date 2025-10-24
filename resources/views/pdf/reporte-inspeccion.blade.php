<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Inspección - {{ $inspection->vessel->name }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm 15mm 20mm 15mm;
            padding: 0;
            @bottom-center {
                content: "Página " counter(page) " de " counter(pages);
                font-size: 9pt;
                color: #666;
            }
        }

        @page :first {
            margin: 0;
            padding: 0;
            @bottom-center {
                content: none;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.6;
            color: #1f1f1f;
        }

        /* ESTILOS CORPORATIVOS */
        :root {
            --primary: #1F4E79;
            --primary-light: #2E75B6;
            --accent: #5B9BD5;
            --success: #70AD47;
            --warning: #FFC000;
            --danger: #C5504B;
            --text-dark: #1F1F1F;
            --text-light: #666666;
            --border-light: #D9D9D9;
            --bg-light: #F2F2F2;
        }

        /* PRIMERA PÁGINA - PORTADA */
        .cover-page {
            page-break-after: always;
            width: 100%;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px 50px;
            position: relative;
            overflow: hidden;
            margin: 0;
            padding-top: 60px;
            padding-bottom: 60px;
        }

        .cover-page::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            transform: translate(100px, -100px);
        }

        .cover-page::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            transform: translate(-100px, 100px);
        }

        .cover-header {
            position: relative;
            z-index: 1;
            text-align: center;
            margin-bottom: 40px;
            width: 100%;
        }

        .cover-logo {
            margin-bottom: 20px;
        }

        .cover-logo img {
            max-height: 70px;
            display: inline-block;
        }

        .cover-title {
            font-size: 42pt;
            font-weight: bold;
            margin: 15px 0;
            line-height: 1.1;
            letter-spacing: 1px;
        }

        .cover-subtitle {
            font-size: 16pt;
            font-weight: 300;
            margin-bottom: 30px;
            opacity: 0.95;
            letter-spacing: 0.5px;
        }

        .cover-content {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 500px;
            margin: 30px auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .cover-info-block {
            background: rgba(255,255,255,0.12);
            border-left: 4px solid white;
            padding: 16px 18px;
            margin-bottom: 12px;
            backdrop-filter: blur(10px);
        }

        .cover-info-label {
            font-size: 8pt;
            opacity: 0.85;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .cover-info-value {
            font-size: 13pt;
            font-weight: bold;
            line-height: 1.3;
        }

        .cover-footer {
            position: relative;
            z-index: 1;
            border-top: 2px solid rgba(255,255,255,0.3);
            padding-top: 25px;
            text-align: center;
            margin-top: 40px;
            width: 100%;
        }

        .cover-date {
            font-size: 10pt;
            opacity: 0.9;
            font-weight: 500;
        }

        /* CONTENIDO PRINCIPAL */
        .content-page {
            padding: 30px 35px;
            page-break-before: always;
            margin: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            padding-top: 5px;
            border-bottom: 3px solid var(--primary);
            position: relative;
        }

        .header-line {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light), var(--accent));
        }

        .header-logo {
            margin-bottom: 10px;
        }

        .header-logo img {
            max-height: 45px;
            display: inline-block;
        }

        .header h1 {
            color: var(--primary);
            font-size: 18pt;
            margin: 8px 0 3px 0;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .header p {
            color: var(--text-light);
            font-size: 8.5pt;
            margin: 0;
            font-weight: 500;
        }

        /* SECCIONES DE INFORMACIÓN */
        .info-section {
            background-color: #fafafa;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-left: 4px solid var(--primary);
            border-right: 1px solid var(--border-light);
            border-top: 1px solid var(--border-light);
            border-bottom: 1px solid var(--border-light);
        }

        .section-title {
            font-size: 11pt;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px 30px;
        }

        .info-grid.full {
            grid-template-columns: 1fr;
        }

        .info-row {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-weight: 600;
            color: var(--primary);
            font-size: 8.5pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 3px;
        }

        .info-value {
            color: var(--text-dark);
            font-size: 10pt;
            font-weight: 500;
        }

        /* BADGES DE ESTADO */
        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            font-weight: 600;
            font-size: 9pt;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-apto {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-no-apto {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status-observado {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        /* PARTES DEL CHECKLIST */
        .parte-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .parte-title {
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            color: white;
            padding: 12px 18px;
            font-size: 11.5pt;
            font-weight: 700;
            margin-bottom: 2px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .parte-count {
            background: rgba(0,0,0,0.1);
            color: white;
            padding: 8px 18px;
            font-size: 8.5pt;
            font-weight: 500;
            margin-bottom: 12px;
        }

        /* TABLAS DE ITEMS */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .items-table thead {
            background: linear-gradient(180deg, #1F4E79, #1a3a5c) !important;
        }

        .items-table thead th {
            background: inherit !important;
            color: white !important;
            padding: 12px 12px;
            text-align: left;
            font-size: 8.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border: 1px solid #1a3a5c !important;
            vertical-align: middle;
        }

        .items-table tbody td {
            padding: 10px 12px;
            border: 1px solid var(--border-light);
            font-size: 9pt;
            vertical-align: top;
        }

        .items-table tbody tr:nth-child(odd) {
            background-color: #fafafa;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: white;
        }

        .items-table tbody tr:hover {
            background-color: #f0f7ff;
        }

        .items-table th:first-child,
        .items-table td:first-child {
            width: 6%;
            text-align: center;
            font-weight: 600;
        }

        /* ITEM COLUMNS */
        .col-item {
            width: 50%;
        }

        .col-estado {
            width: 18%;
            text-align: center;
        }

        .col-observaciones {
            width: 26%;
        }

        /* ESTADO BADGES EN TABLA */
        .estado-badge {
            display: inline-block;
            padding: 4px 8px;
            font-weight: 600;
            font-size: 7.5pt;
            border-radius: 3px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .estado-A {
            background-color: #d4edda;
            color: #155724;
            border: 0.5px solid #c3e6cb;
        }

        .estado-N {
            background-color: #f8d7da;
            color: #721c24;
            border: 0.5px solid #f5c6cb;
        }

        .estado-O {
            background-color: #fff3cd;
            color: #856404;
            border: 0.5px solid #ffeaa7;
        }

        /* CAJA DE RESUMEN */
        .summary-box {
            background: linear-gradient(135deg, #fafafa, white);
            border: 2px solid var(--primary);
            border-radius: 4px;
            padding: 20px;
            margin-top: 30px;
            margin-bottom: 20px;
            page-break-inside: avoid;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .summary-title {
            font-size: 12pt;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 15px;
            letter-spacing: 0.3px;
            text-align: center;
            text-transform: uppercase;
        }

        .summary-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .summary-stat {
            text-align: center;
            padding: 15px;
            background: white;
            border-left: 3px solid var(--border-light);
            border-radius: 2px;
        }

        .summary-stat-value {
            font-size: 22pt;
            font-weight: 700;
            display: block;
            margin-bottom: 5px;
        }

        .summary-stat-label {
            font-size: 8pt;
            color: var(--text-light);
            display: block;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .summary-stat.apto .summary-stat-value { color: var(--success); }
        .summary-stat.no-apto .summary-stat-value { color: var(--danger); }
        .summary-stat.observado .summary-stat-value { color: var(--warning); }

        .summary-stat.apto { border-left-color: var(--success); }
        .summary-stat.no-apto { border-left-color: var(--danger); }
        .summary-stat.observado { border-left-color: var(--warning); }

        /* OBSERVACIONES */
        .observations-section {
            background-color: #fffaf0;
            border-left: 4px solid var(--warning);
            border-right: 1px solid var(--border-light);
            border-top: 1px solid var(--border-light);
            border-bottom: 1px solid var(--border-light);
            padding: 15px 20px;
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .observations-title {
            font-weight: 700;
            color: var(--primary);
            font-size: 10pt;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .observations-text {
            color: var(--text-dark);
            line-height: 1.6;
            font-size: 9.5pt;
        }

        /* PIE DE PÁGINA */
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 2px solid var(--border-light);
            text-align: center;
            font-size: 8pt;
            color: var(--text-light);
            line-height: 1.6;
            position: relative;
        }

        .footer p {
            margin: 3px 0;
        }

        .footer-divider {
            display: inline-block;
            margin: 0 8px;
            color: var(--border-light);
        }

        /* UTILITY */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        .font-bold {
            font-weight: 700;
        }

        .text-primary {
            color: var(--primary);
        }

        .break-before {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- PRIMERA PÁGINA - PORTADA -->
    <div class="cover-page">
        <div class="cover-header">
            <div class="cover-logo">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo Novum Energy">
            </div>
            <div class="cover-title">INFORME DE INSPECCIÓN</div>
            <div class="cover-subtitle">Checklist de Inspección Marítima</div>
        </div>

        <div class="cover-content">
            <div class="cover-info-block">
                <div class="cover-info-label">Propietario / Armador</div>
                <div class="cover-info-value">{{ $inspection->owner->name }}</div>
            </div>

            <div class="cover-info-block">
                <div class="cover-info-label">Embarcación</div>
                <div class="cover-info-value">{{ $inspection->vessel->name }}</div>
            </div>

            <div class="cover-info-block">
                <div class="cover-info-label">Estado General de Inspección</div>
                <div class="cover-info-value">
                    @if(strtoupper($inspection->overall_status) === 'A' || strpos(strtoupper($inspection->overall_status), 'APTO') !== false)
                        ✓ APTO
                    @elseif(strtoupper($inspection->overall_status) === 'N' || strpos(strtoupper($inspection->overall_status), 'NO APTO') !== false)
                        ✗ NO APTO
                    @elseif(strtoupper($inspection->overall_status) === 'O' || strpos(strtoupper($inspection->overall_status), 'OBSERVADO') !== false)
                        ⚠ OBSERVADO
                    @else
                        {{ $inspection->overall_status }}
                    @endif
                </div>
            </div>
        </div>

        <div class="cover-footer">
            <div class="cover-date">{{ now()->format('d/m/Y') }}</div>
            <div style="font-size: 8pt; margin-top: 8px; opacity: 0.8;">
                <strong>Inspector:</strong> {{ $inspection->inspector_name ?? 'No asignado' }}
            </div>
        </div>
    </div>

    <!-- PÁGINA DE CONTENIDO -->
    <div class="content-page">
        <!-- Encabezado -->
        <div class="header">
            <div class="header-line"></div>
            <div class="header-logo">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo Novum Energy">
            </div>
            <h1>INFORME DE INSPECCIÓN CHECKLIST</h1>
            <p>{{ now()->format('d/m/Y') }}</p>
        </div>

        <!-- Información General -->
        <div class="info-section">
            <div class="section-title">Información General de la Inspección</div>
            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">Propietario / Armador</span>
                    <span class="info-value">{{ $inspection->owner->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Embarcación</span>
                    <span class="info-value">{{ $inspection->vessel->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Inspector Responsable</span>
                    <span class="info-value">{{ $inspection->inspector_name ?? 'No asignado' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Período de Inspección</span>
                    <span class="info-value">{{ $inspection->inspection_start_date->format('d/m/Y') }} - {{ $inspection->inspection_end_date ? $inspection->inspection_end_date->format('d/m/Y') : 'En proceso' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Estado General de Inspección</span>
                    <span class="info-value">
                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $inspection->overall_status)) }}" style="display: inline-block;">
                            {{ strtoupper($inspection->overall_status) }}
                        </span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Documento Generado</span>
                    <span class="info-value">{{ now()->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Partes del Checklist -->
        @foreach($partes as $parteNum => $parteData)
            @if(!empty($parteData['items']))
                <div class="parte-section break-before">
                    <div class="parte-title">{{ $parteData['title'] }}</div>
                    <div class="parte-count">{{ count($parteData['items']) }} ítems de evaluación en esta sección</div>

                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class="col-item">Ítem de Inspección</th>
                                <th class="col-estado">Estado</th>
                                <th class="col-observaciones">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($parteData['items'] as $index => $item)
                                <tr>
                                    <td style="text-align: center; font-weight: 600;">{{ $index + 1 }}</td>
                                    <td class="col-item">
                                        <div style="font-weight: 500; margin-bottom: 2px;">{{ $item['item'] }}</div>
                                        @if(!empty($item['item_es']))
                                            <div style="font-size: 8.5pt; color: #999; font-style: italic;">{{ $item['item_es'] }}</div>
                                        @endif
                                    </td>
                                    <td class="col-estado">
                                        @if($item['estado'] == 'A')
                                            <span class="estado-badge estado-A">✓ Apto</span>
                                        @elseif($item['estado'] == 'N')
                                            <span class="estado-badge estado-N">✗ No Apto</span>
                                        @elseif($item['estado'] == 'O')
                                            <span class="estado-badge estado-O">⚠ Observado</span>
                                        @else
                                            <span style="color: #999; font-size: 8pt;">No evaluado</span>
                                        @endif
                                    </td>
                                    <td class="col-observaciones">
                                        <span style="color: #666;">{{ !empty($item['comentarios']) ? $item['comentarios'] : '—' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endforeach

        <!-- Resumen Estadístico -->
        <div class="summary-box">
            <div class="summary-title">📊 Resumen Estadístico de la Inspección</div>
            <div class="summary-stats">
                <div class="summary-stat apto">
                    <span class="summary-stat-value">{{ $stats['apto'] }}</span>
                    <span class="summary-stat-label">Apto</span>
                </div>
                <div class="summary-stat no-apto">
                    <span class="summary-stat-value">{{ $stats['no_apto'] }}</span>
                    <span class="summary-stat-label">No Apto</span>
                </div>
                <div class="summary-stat observado">
                    <span class="summary-stat-value">{{ $stats['observado'] }}</span>
                    <span class="summary-stat-label">Observado</span>
                </div>
            </div>
        </div>

        <!-- Estadísticas Detalladas -->
        <div class="info-section" style="margin-top: 20px;">
            <div class="section-title">Estadísticas Detalladas</div>
            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">Total de Ítems Evaluados</span>
                    <span class="info-value">{{ $stats['total'] }} ítems</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Porcentaje de Cumplimiento</span>
                    <span class="info-value" style="color: #70AD47; font-weight: 700;">{{ $stats['porcentaje_cumplimiento'] }}%</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tasa de Conformidad</span>
                    <span class="info-value">
                        @if($stats['total'] > 0)
                            {{ round(($stats['apto'] / $stats['total']) * 100, 1) }}% de ítems sin problemas
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nivel de Riesgo</span>
                    <span class="info-value">
                        @if($stats['no_apto'] > 0)
                            <span style="color: #C5504B; font-weight: 700;">⚠ CRÍTICO</span>
                        @elseif($stats['observado'] > 0)
                            <span style="color: #FFC000; font-weight: 700;">⚡ MEDIO</span>
                        @else
                            <span style="color: #70AD47; font-weight: 700;">✓ BAJO</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Observaciones Generales -->
        @if($inspection->general_observations)
            <div class="observations-section">
                <div class="observations-title">📝 Observaciones Generales</div>
                <div class="observations-text">{{ $inspection->general_observations }}</div>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>🔒 Documento confidencial - Sistema de Gestión de Inspecciones Marítimas <strong>Navio</strong></p>
            <div class="footer-divider">|</div>
            <p>Generado: {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i:s') }}</p>
            <p style="margin-top: 10px; font-size: 7pt; color: #aaa;">{{ config('app.name', 'Navio') }} © {{ date('Y') }} - Todos los derechos reservados</p>
        </div>
    </div>
</body>
</html>

