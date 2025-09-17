<?php

namespace App\Filament\Pages;

use App\Models\InspectionSchedule;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class InspectionCalendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static string $view = 'filament.pages.inspection-calendar.index';

    protected static ?string $navigationGroup = 'Inspecciones';

    protected static ?string $navigationLabel = 'Calendario de Inspecciones';

    protected static ?int $navigationSort = 7;

    public $year;
    public $month;

    public function mount(): void
    {
        $this->year = now()->year;
        $this->month = now()->month;
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->year = $date->year;
        $this->month = $date->month;
        $this->dispatch('refresh-calendar');
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->year = $date->year;
        $this->month = $date->month;
        $this->dispatch('refresh-calendar');
    }

    public function goToToday(): void
    {
        $this->year = now()->year;
        $this->month = now()->month;
        $this->dispatch('refresh-calendar');
    }

    public function getViewData(): array
    {
        // Crear fecha de inicio y fin para el mes
        $startDate = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $endDate = Carbon::create($this->year, $this->month, 1)->endOfMonth();

        // Añadir log para depuración
        \Log::info("Buscando inspecciones entre {$startDate} y {$endDate}");

        // Obtener inspecciones para el mes
        $inspectionQuery = InspectionSchedule::whereBetween('start_datetime', [$startDate, $endDate])
            ->with('vessel');

        // Si el usuario tiene el rol "Armador", solo mostrar inspecciones de embarcaciones asignadas a él
        if (auth()->check() && auth()->user()->hasRole('Armador')) {
            $assignedVesselIds = auth()->user()->vessels()->pluck('id');
            $inspectionQuery->whereIn('vessel_id', $assignedVesselIds);
        }

        $inspections = $inspectionQuery->get();

        // Log del número de inspecciones encontradas
        \Log::info("Inspecciones encontradas: " . $inspections->count());

        // Log de los estados de las inspecciones
        $statuses = $inspections->pluck('status')->unique()->toArray();
        \Log::info("Estados encontrados: " . implode(", ", $statuses));

        $mappedInspections = $inspections->map(function ($inspection) {
            return [
                'id' => $inspection->id,
                'title' => $inspection->title,
                'start' => $inspection->start_datetime->toIso8601String(),
                'end' => $inspection->end_datetime->toIso8601String(),
                'vessel_name' => $inspection->vessel->name ?? 'N/A',
                'status' => $inspection->status,
                'status_label' => InspectionSchedule::getStatusLabel($inspection->status),
                'status_color' => match (strtolower($inspection->status)) {
                    'scheduled', 'programada' => 'style="background-color: #3b82f6; color: white;"',
                    'completed', 'completada' => 'style="background-color: #22c55e; color: white;"',
                    'cancelled', 'cancelada' => 'style="background-color: #ef4444; color: white;"',
                    default => 'style="background-color: #6b7280; color: white;"',
                }
            ];
        });

        // Datos para el calendario
        $firstDayOfMonth = Carbon::create($this->year, $this->month, 1)->dayOfWeek;
        $daysInMonth = Carbon::create($this->year, $this->month, 1)->daysInMonth;

        return [
            'inspections' => $mappedInspections,
            'year' => $this->year,
            'month' => $this->month,
            'firstDayOfMonth' => $firstDayOfMonth,
            'daysInMonth' => $daysInMonth,
        ];
    }
}