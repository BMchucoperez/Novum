<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\ChecklistInspection;
use App\Models\InspectionSchedule;

class InspectorStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('Inspector') ?? false;
    }

    protected function getStats(): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $today = today();

        // Inspecciones completadas este mes
        $completedThisMonth = ChecklistInspection::query()
            ->whereYear('inspection_end_date', $currentYear)
            ->whereMonth('inspection_end_date', $currentMonth)
            ->count();

        // Inspecciones programadas (próximas y sin completar)
        $scheduledUpcoming = InspectionSchedule::query()
            ->where('status', 'scheduled')
            ->where('start_datetime', '>=', now())
            ->count();

        // Inspecciones completadas hoy
        $completedToday = ChecklistInspection::query()
            ->whereDate('inspection_end_date', $today)
            ->count();

        // Embarcaciones distintas inspeccionadas (total histórico)
        $distinctVessels = ChecklistInspection::query()
            ->distinct('vessel_id')
            ->count('vessel_id');

        // Inspecciones con problemas críticos (No Apto)
        $criticalIssues = ChecklistInspection::query()
            ->where('overall_status', 'N')
            ->count();

        return [
            Stat::make('Inspecciones Este Mes', $completedThisMonth)
                ->description('Completadas en ' . now()->format('M Y'))
                ->color('primary')
                ->icon('heroicon-o-calendar'),

            Stat::make('Inspecciones Programadas', $scheduledUpcoming)
                ->description('Próximas a realizar')
                ->color('info')
                ->icon('heroicon-o-clock'),

            Stat::make('Completadas Hoy', $completedToday)
                ->description('Estado: Finalizadas')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Embarcaciones Distintas', $distinctVessels)
                ->description('Inspeccionadas (total)')
                ->color('warning')
                ->icon('heroicon-o-ship'),

            Stat::make('Problemas Críticos', $criticalIssues)
                ->description('Estado: No Apto')
                ->color('danger')
                ->icon('heroicon-o-exclamation-circle'),
        ];
    }
}
