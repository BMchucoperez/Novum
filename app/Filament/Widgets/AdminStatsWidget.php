<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Vessel;
use App\Models\User;
use App\Models\ChecklistInspection;
use App\Models\VesselDocument;

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
        $criticalIssues = ChecklistInspection::where('overall_status', 'N')->count();
        $expiredDocuments = VesselDocument::expired()->count();
        $inspectionsThisWeek = ChecklistInspection::query()
            ->whereBetween('inspection_start_date', [$currentWeekStart, $currentWeekEnd])
            ->count();

        return [
            Stat::make('Total Embarcaciones', $totalVessels)
                ->description('Registradas en el sistema')
                ->color('primary')
                ->icon('heroicon-o-ship'),

            Stat::make('Total Usuarios', $totalUsers)
                ->description('Usuarios activos')
                ->color('info')
                ->icon('heroicon-o-user-group'),

            Stat::make('Total Inspecciones', $totalInspections)
                ->description('Historial completo')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Problemas Críticos', $criticalIssues)
                ->description('Estado: No Apto')
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle'),

            Stat::make('Documentos Vencidos', $expiredDocuments)
                ->description('Requieren renovación')
                ->color('warning')
                ->icon('heroicon-o-calendar'),

            Stat::make('Inspecciones Esta Semana', $inspectionsThisWeek)
                ->description('Semana actual')
                ->color('secondary')
                ->icon('heroicon-o-calendar-days'),
        ];
    }
}
