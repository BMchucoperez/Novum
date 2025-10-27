<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as FilamentDashboard;
use App\Filament\Widgets\ArmadorStatsWidget;
use App\Filament\Widgets\ArmadorLastInspectionsWidget;
use App\Filament\Widgets\InspectorStatsWidget;
use App\Filament\Widgets\InspectorUpcomingSchedulesWidget;
use App\Filament\Widgets\InspectorCompletedInspectionsWidget;
use App\Filament\Widgets\AdminStatsWidget;
use App\Filament\Widgets\AdminCriticalIssuesWidget;
use App\Filament\Widgets\AdminRecentInspectionsWidget;

class Dashboard extends FilamentDashboard
{
    public function getTitle(): string
    {
        $user = auth()->user();

        if ($user->hasRole('Armador')) {
            return 'Mi Dashboard - Propietario';
        } elseif ($user->hasRole('Inspector')) {
            return 'Mi Dashboard - Inspector';
        }

        return 'Dashboard del Sistema';
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        $user = auth()->user();

        // Dashboard para Armador (propietario)
        if ($user->hasRole('Armador')) {
            return [
                ArmadorStatsWidget::class,
                ArmadorLastInspectionsWidget::class,
            ];
        }

        // Dashboard para Inspector
        if ($user->hasRole('Inspector')) {
            return [
                InspectorStatsWidget::class,
                InspectorUpcomingSchedulesWidget::class,
                InspectorCompletedInspectionsWidget::class,
            ];
        }

        // Dashboard para Admin (por defecto)
        return [
            AdminStatsWidget::class,
            AdminCriticalIssuesWidget::class,
            AdminRecentInspectionsWidget::class,
        ];
    }
}
