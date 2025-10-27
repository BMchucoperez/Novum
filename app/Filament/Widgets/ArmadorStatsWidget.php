<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Vessel;
use App\Models\ChecklistInspection;
use App\Models\VesselDocument;
use App\Models\InspectionSchedule;

class ArmadorStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('Armador') ?? false;
    }

    protected function getStats(): array
    {
        $userId = auth()->id();

        // Embarcaciones del usuario (solo sus IDs)
        $vesselIds = Vessel::where('user_id', $userId)->pluck('id')->toArray();

        $totalVessels = count($vesselIds);
        $aptInspections = ChecklistInspection::whereIn('vessel_id', $vesselIds)
            ->where('overall_status', 'A')
            ->count();

        $problemInspections = ChecklistInspection::whereIn('vessel_id', $vesselIds)
            ->whereIn('overall_status', ['N', 'O'])
            ->count();

        $expiringDocuments = VesselDocument::whereIn('vessel_id', $vesselIds)
            ->expiring(30)
            ->count();

        $completedSchedules = InspectionSchedule::whereIn('vessel_id', $vesselIds)
            ->where('status', 'completed')
            ->count();

        return [
            Stat::make('Embarcaciones', $totalVessels)
                ->description('Total de embarcaciones propias')
                ->color('primary')
                ->icon('heroicon-o-ship'),

            Stat::make('Inspecciones Aptas', $aptInspections)
                ->description('Estado: Apto')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Inspecciones con Problemas', $problemInspections)
                ->description('Estado: No Apto u Observado')
                ->color('warning')
                ->icon('heroicon-o-exclamation-circle'),

            Stat::make('Documentos por Vencer', $expiringDocuments)
                ->description('Próximos 30 días')
                ->color('danger')
                ->icon('heroicon-o-clock'),

            Stat::make('Inspecciones Completadas', $completedSchedules)
                ->description('Total realizadas')
                ->color('info')
                ->icon('heroicon-o-check'),
        ];
    }
}
