<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Vessel;
use App\Models\Owner;
use App\Models\Shipyard;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 0;
    
    protected function getStats(): array
    {
        return [
            Stat::make('Total de Embarcaciones', Vessel::count())
                ->description('Embarcaciones registradas')
                ->descriptionIcon('heroicon-m-truck')
                ->color('success'),
                
            Stat::make('Total de Usuarios', User::count())
                ->description('Usuarios del sistema')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
                
            Stat::make('Total de Propietarios', Owner::count())
                ->description('Propietarios registrados')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),
                
            Stat::make('Total de Astilleros', Shipyard::count())
                ->description('Astilleros registrados')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('danger'),
        ];
    }
}
