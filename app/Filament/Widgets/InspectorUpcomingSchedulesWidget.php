<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\InspectionSchedule;

class InspectorUpcomingSchedulesWidget extends BaseWidget
{
    protected static ?string $heading = 'Próximas Inspecciones Programadas';
    protected static ?int $defaultPaginationPageOption = 5;
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('Inspector') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                InspectionSchedule::query()
                    ->where('status', 'scheduled')
                    ->where('start_datetime', '>=', now())
                    ->with('vessel')
                    ->orderBy('start_datetime', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('vessel.name')
                    ->label('Embarcación')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('start_datetime')
                    ->label('Inicio')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('location')
                    ->label('Ubicación')
                    ->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Descripción')
                    ->limit(30),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'scheduled' => 'Programada',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                        default => $state,
                    })
                    ->color(function ($state) {
                        return match($state) {
                            'scheduled' => 'info',
                            'completed' => 'success',
                            'cancelled' => 'danger',
                            default => 'gray',
                        };
                    }),
            ]);
    }
}
