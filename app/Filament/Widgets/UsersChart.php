<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;

class UsersChart extends ApexChartWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 1;

    /**
     * Chart Id
     *
     * @var string|null
     */
    protected static ?string $chartId = 'usersChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Usuarios por Rol';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $data = $this->getUserData();

        return [
            'chart' => [
                'type' => 'pie',
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
            'colors' => ['#3b82f6', '#8b5cf6', '#ec4899', '#f97316', '#10b981'],
            'plotOptions' => [
                'pie' => [
                    'dataLabels' => [
                        'offset' => -10,
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
                'enabled' => true,
                'style' => [
                    'fontSize' => '14px',
                    'fontFamily' => 'inherit',
                    'fontWeight' => 'bold',
                ],
            ],
            'grid' => [
                'show' => false,
            ],
        ];
    }

    protected function getUserData(): array
    {
        $roles = Role::withCount('users')->get();
        $usersWithoutRole = User::whereDoesntHave('roles')->count();

        $labels = $roles->pluck('name')->toArray();
        $counts = $roles->pluck('users_count')->toArray();

        // Añadir usuarios sin rol
        if ($usersWithoutRole > 0) {
            $labels[] = 'Sin rol';
            $counts[] = $usersWithoutRole;
        }

        // Si no hay datos, mostrar un valor por defecto
        if (empty($labels)) {
            $labels = ['Usuarios'];
            $counts = [User::count()];
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
        ];
    }
}
