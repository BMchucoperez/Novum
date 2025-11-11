<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Vessel;
use App\Models\User;
use App\Models\ChecklistInspection;

class AdminStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        $user = auth()->user();
        return !$user || !($user->hasRole('Armador') || $user->hasRole('Inspector'));
    }

    protected function getStats(): array
    {
        $currentWeekStart = now()->startOfWeek();
        $currentWeekEnd = now()->endOfWeek();

        // Métricas globales
        $totalVessels = Vessel::count();
        $totalUsers = User::count();
        $totalInspections = ChecklistInspection::count();
        $criticalIssues = ChecklistInspection::where('overall_status', 'NO APTO')->count();
        $inspectionsThisWeek = ChecklistInspection::query()
            ->whereBetween('inspection_start_date', [$currentWeekStart, $currentWeekEnd])
            ->count();

        return [
            Stat::make('Total Embarcaciones', $totalVessels)
                ->description('Registradas en el sistema')
                ->color('primary'),

            Stat::make('Total Usuarios', $totalUsers)
                ->description('Usuarios activos')
                ->color('info'),

            Stat::make('Total Inspecciones', $totalInspections)
                ->description('Historial completo')
                ->color('success'),

            Stat::make('Problemas Críticos', $criticalIssues)
                ->description('Estado: No Apto')
                ->color('danger'),

            Stat::make('Inspecciones Esta Semana', $inspectionsThisWeek)
                ->description('Semana actual')
                ->color('secondary'),
        ];
    }
}
