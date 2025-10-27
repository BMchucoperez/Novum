<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\ChecklistInspection;

class AdminRecentInspectionsWidget extends BaseWidget
{
    protected static ?string $heading = 'Últimas Inspecciones';
    protected static ?int $defaultPaginationPageOption = 10;
    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        $user = auth()->user();
        return !$user || !($user->hasRole('Armador') || $user->hasRole('Inspector'));
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ChecklistInspection::query()
                    ->with('vessel', 'owner')
                    ->latest('inspection_end_date')
            )
            ->columns([
                Tables\Columns\TextColumn::make('vessel.name')
                    ->label('Embarcación')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Propietario')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('inspection_start_date')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('inspection_end_date')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('inspector_name')
                    ->label('Inspector')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('overall_status')
                    ->label('Estado')
                    ->formatStateUsing(function ($state) {
                        return match($state) {
                            'A' => 'Apto',
                            'N' => 'No Apto',
                            'O' => 'Observado',
                            default => $state,
                        };
                    })
                    ->color(function ($state) {
                        return match($state) {
                            'A' => 'success',
                            'N' => 'danger',
                            'O' => 'warning',
                            default => 'gray',
                        };
                    }),
            ]);
    }
}
