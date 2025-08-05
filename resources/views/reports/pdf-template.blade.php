<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Inspección</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .item {
            margin-bottom: 8px;
            padding-left: 15px;
        }
        .item-title {
            font-weight: bold;
            margin-bottom: 3px;
        }
        .observations {
            background-color: #f9f9f9;
            padding: 10px;
            border-left: 4px solid #007cba;
            margin-top: 10px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">REPORTE DE INSPECCIÓN</div>
        <div class="info-section">
            <div><span class="info-label">Fecha:</span> {{ $fecha }}</div>
            <div><span class="info-label">Inspector:</span> {{ $inspector }}</div>
            <div><span class="info-label">Propietario:</span> {{ $propietario }}</div>
            <div><span class="info-label">Embarcación:</span> {{ $embarcacion }}</div>
        </div>
    </div>

    @if(!empty($structureData))
        <div class="section-title">ESTRUCTURA Y MAQUINARIA</div>
        @foreach($structureData as $item)
            <div class="item">
                <div class="item-title">{{ $item['name'] ?? 'Sin nombre' }}</div>
                @if(!empty($item['observations']))
                    <div class="observations">{{ $item['observations'] }}</div>
                @endif
            </div>
        @endforeach
    @endif

    @if(!empty($certificateData))
        <div class="section-title">CERTIFICADOS ESTATUTARIOS</div>
        @foreach($certificateData as $item)
            <div class="item">
                <div class="item-title">{{ $item['name'] ?? 'Sin nombre' }}</div>
                @if(!empty($item['observations']))
                    <div class="observations">{{ $item['observations'] }}</div>
                @endif
            </div>
        @endforeach
    @endif

    @if(!empty($documentData))
        <div class="section-title">DOCUMENTOS DE GESTIÓN A BORDO</div>
        @foreach($documentData as $item)
            <div class="item">
                <div class="item-title">{{ $item['name'] ?? 'Sin nombre' }}</div>
                @if(!empty($item['observations']))
                    <div class="observations">{{ $item['observations'] }}</div>
                @endif
            </div>
        @endforeach
    @endif

    @if(!empty($crewData))
        <div class="section-title">TRIPULACIÓN</div>
        @foreach($crewData as $item)
            <div class="item">
                <div class="item-title">{{ $item['name'] ?? 'Sin nombre' }}</div>
                @if(!empty($item['observations']))
                    <div class="observations">{{ $item['observations'] }}</div>
                @endif
            </div>
        @endforeach
    @endif

    @if(!empty($generalObservations))
        <div class="section-title">OBSERVACIONES GENERALES</div>
        <div class="observations">{{ $generalObservations }}</div>
    @endif
</body>
</html>