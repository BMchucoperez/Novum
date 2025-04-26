<?php

namespace App\Filament\Widgets;

use App\Models\Vessel;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Carbon;

class VesselsChart extends ApexChartWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 1;

    /**
     * Chart Id
     *
     * @var string|null
     */
    protected static ?string $chartId = 'vesselsChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Embarcaciones Registradas';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $data = $this->getVesselData();

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
                'toolbar' => [
                    'show' => false,
                ],
                'animations' => [
                    'enabled' => true,
                    'speed' => 300,
                ],
            ],
            'series' => [$data['total']],
            'labels' => ['Total de Embarcaciones'],
            'legend' => [
                'labels' => [
                    'colors' => '#9ca3af',
                    'fontWeight' => 600,
                ],
            ],
            'colors' => ['#10b981'],
            'plotOptions' => [
                'pie' => [
                    'donut' => [
                        'size' => '65%',
                        'labels' => [
                            'show' => true,
                            'name' => [
                                'show' => true,
                                'fontWeight' => 600,
                            ],
                            'value' => [
                                'show' => true,
                                'fontWeight' => 600,
                                'formatter' => 'function (val) { return val }',
                            ],
                            'total' => [
                                'show' => true,
                                'label' => 'Total',
                                'fontWeight' => 600,
                                'formatter' => 'function (w) { return w.globals.seriesTotals.reduce((a, b) => { return a + b }, 0) }',
                            ],
                        ],
                    ],
                ],
            ],
            'stroke' => [
                'width' => 0,
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
            'dataLabels' => [
                'enabled' => false,
            ],
            'grid' => [
                'show' => false,
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
            'xaxis' => [
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontSize' => '14px',
                        'fontWeight' => 500,
                    ],
                ],
            ],
        ];
    }

    protected function getVesselData(): array
    {
        $total = Vessel::count();

        return [
            'total' => $total,
        ];
    }
}
