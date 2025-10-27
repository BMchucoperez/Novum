<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Vessel;
use App\Models\ChecklistInspection;
use Illuminate\Database\Eloquent\Builder;

class ArmadorLastInspectionsWidget extends BaseWidget
{
    protected static ?string $heading = 'Últimas Inspecciones';
    protected static ?int $defaultPaginationPageOption = 10;
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('Armador') ?? false;
    }

    public function table(Table $table): Table
    {
        $userId = auth()->id();
        $vesselIds = Vessel::where('user_id', $userId)->pluck('id')->toArray();

        return $table
            ->query(
                ChecklistInspection::query()
                    ->whereIn('vessel_id', $vesselIds)
                    ->with('vessel')
                    ->latest('inspection_end_date')
            )
            ->columns([
                Tables\Columns\TextColumn::make('vessel.name')
                    ->label('Embarcación')
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
