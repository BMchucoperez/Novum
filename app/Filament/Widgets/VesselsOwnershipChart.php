<?php

namespace App\Filament\Widgets;

use App\Models\Vessel;
use App\Models\Owner;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class VesselsOwnershipChart extends ApexChartWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 1;

    /**
     * Chart Id
     *
     * @var string|null
     */
    protected static ?string $chartId = 'vesselsOwnershipChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Distribución de Propietarios';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $data = $this->getOwnershipData();

        return [
            'chart' => [
                'type' => 'polarArea',
                'height' => 300,
                'toolbar' => [
                    'show' => false,
                ],
                'animations' => [
                    'enabled' => true,
                    'speed' => 300,
                ],
            ],
            'series' => $data['counts'],
            'labels' => $data['labels'],
            'legend' => [
                'position' => 'bottom',
                'horizontalAlign' => 'center',
                'labels' => [
                    'colors' => '#9ca3af',
                    'fontWeight' => 600,
                ],
            ],
            'colors' => ['#3b82f6', '#8b5cf6', '#ec4899', '#f97316', '#10b981', '#0ea5e9', '#f59e0b'],
            'plotOptions' => [
                'polarArea' => [
                    'rings' => [
                        'strokeWidth' => 1,
                        'strokeColor' => '#e5e7eb',
                    ],
                    'spokes' => [
                        'strokeWidth' => 1,
                        'connectorColors' => '#e5e7eb',
                    ],
                ],
            ],
            'stroke' => [
                'width' => 1,
                'colors' => ['#ffffff'],
            ],
            'fill' => [
                'opacity' => 0.8,
                'type' => 'solid',
            ],
            'tooltip' => [
                'style' => [
                    'fontSize' => '14px',
                    'fontFamily' => 'inherit',
                ],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'yaxis' => [
                'show' => false,
            ],
        ];
    }

    protected function getOwnershipData(): array
    {
        // Obtener los 5 propietarios con más embarcaciones
        $topOwners = Owner::withCount('vessels')
            ->orderBy('vessels_count', 'desc')
            ->limit(5)
            ->get();

        // Contar el resto de embarcaciones
        $totalVessels = Vessel::count();
        $topOwnersVessels = $topOwners->sum('vessels_count');
        $otherVessels = $totalVessels - $topOwnersVessels;

        $labels = $topOwners->pluck('name')->toArray();
        $counts = $topOwners->pluck('vessels_count')->toArray();

        // Añadir "Otros" si hay más propietarios
        if ($otherVessels > 0) {
            $labels[] = 'Otros';
            $counts[] = $otherVessels;
        }

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
