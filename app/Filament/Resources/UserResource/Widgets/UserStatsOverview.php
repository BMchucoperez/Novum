<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserStatsOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $totalUsers = User::count();
        $recentUsers = User::where('created_at', '>=', now()->subDays(30))->count();

        // Obtener estadísticas de roles si está disponible
        $roleStats = [];
        if (class_exists(Role::class)) {
            $roles = Role::withCount('users')->get();
            foreach ($roles as $role) {
                $percentage = $totalUsers > 0 ? round(($role->users_count / $totalUsers) * 100) : 0;
                $roleStats[] = Stat::make($role->name, $role->users_count)
                    ->description($percentage . '% de usuarios')
                    ->descriptionIcon('heroicon-m-user-group')
                    ->color('warning')
                    ->chart($this->generateRandomChart($role->users_count));
            }
        }

        // Estadísticas principales
        $mainStats = [
            Stat::make('Total de Usuarios', $totalUsers)
                ->description('Usuarios registrados en el sistema')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart($this->generateChartData(30))
                ->extraAttributes([
                    'class' => 'bg-primary-50 dark:bg-primary-950/20 border border-primary-200 dark:border-primary-800 rounded-xl',
                ]),

            Stat::make('Usuarios Nuevos', $recentUsers)
                ->description('En los últimos 30 días')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info')
                ->chart($this->generateChartData(7, 'recent'))
                ->extraAttributes([
                    'class' => 'bg-info-50 dark:bg-info-950/20 border border-info-200 dark:border-info-800 rounded-xl',
                ]),
        ];

        // Combinar estadísticas principales con estadísticas de roles
        return array_merge($mainStats, $roleStats);
    }

    protected function generateChartData(int $days, string $type = 'all'): array
    {
        $data = [];

        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');

            $query = User::whereDate('created_at', $date);

            if ($type === 'recent') {
                // Ya está filtrado por fecha
            }

            $data[] = $query->count();
        }

        // Asegurarse de que hay al menos un valor distinto de cero
        if (array_sum($data) === 0) {
            $data[array_key_last($data)] = 1;
        }

        return $data;
    }

    protected function generateRandomChart($max): array
    {
        $data = [];
        $days = 7;

        for ($i = 0; $i < $days; $i++) {
            $data[] = rand(max(1, $max / 2), $max);
        }

        $data[] = $max;

        return $data;
    }
}
