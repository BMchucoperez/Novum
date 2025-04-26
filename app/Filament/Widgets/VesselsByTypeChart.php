<?php

namespace App\Filament\Widgets;

use App\Models\Vessel;
use App\Models\ServiceType;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class VesselsByTypeChart extends ApexChartWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 1;

    /**
     * Chart Id
     *
     * @var string|null
     */
    protected static ?string $chartId = 'vesselsByTypeChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Embarcaciones por Tipo de Servicio';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $data = $this->getVesselsByTypeData();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'toolbar' => [
                    'show' => false,
                ],
                'animations' => [
                    'enabled' => true,
                    'speed' => 300,
                ],
            ],
            'series' => [
                [
                    'name' => 'Embarcaciones',
                    'data' => $data['counts'],
                ],
            ],
            'xaxis' => [
                'categories' => $data['labels'],
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontSize' => '14px',
                        'fontWeight' => 500,
                    ],
                    'rotate' => -45,
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontSize' => '14px',
                        'fontWeight' => 500,
                    ],
                ],
            ],
            'colors' => ['#10b981'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 5,
                    'horizontal' => false,
                    'columnWidth' => '70%',
                    'distributed' => false,
                    'dataLabels' => [
                        'position' => 'top',
                    ],
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
                'style' => [
                    'fontSize' => '14px',
                    'fontWeight' => 'bold',
                    'colors' => ['#000000'],
                ],
            ],
            'grid' => [
                'show' => true,
                'borderColor' => '#e5e7eb',
                'strokeDashArray' => 0,
                'position' => 'back',
            ],
            'fill' => [
                'opacity' => 1,
                'type' => 'solid',
            ],
            'tooltip' => [
                'style' => [
                    'fontSize' => '14px',
                    'fontFamily' => 'inherit',
                ],
            ],
        ];
    }

    protected function getVesselsByTypeData(): array
    {
        $serviceTypes = ServiceType::withCount('vessels')->get();

        $labels = $serviceTypes->pluck('name')->toArray();
        $counts = $serviceTypes->pluck('vessels_count')->toArray();

        // Si no hay datos, mostrar un valor por defecto
        if (empty($labels)) {
            $labels = ['Sin datos'];
            $counts = [0];
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
        ];
    }
}
