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

    protected static ?int $navigationSort = 3;

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

        // Obtener inspecciones para el mes
        $inspections = InspectionSchedule::whereBetween('start_datetime', [$startDate, $endDate])
            ->with('vessel')
            ->get()
            ->map(function ($inspection) {
                return [
                    'id' => $inspection->id,
                    'title' => $inspection->title,
                    'start' => $inspection->start_datetime->toIso8601String(),
                    'end' => $inspection->end_datetime->toIso8601String(),
                    'vessel_name' => $inspection->vessel->name ?? 'N/A',
                    'status' => $inspection->status,
                    'status_label' => InspectionSchedule::getStatusOptions()[$inspection->status] ?? $inspection->status,
                    'status_color' => match ($inspection->status) {
                        'scheduled' => 'bg-blue-500',
                        'completed' => 'bg-green-500',
                        'cancelled' => 'bg-red-500',
                        default => 'bg-gray-500',
                    }
                ];
            });

        // Datos para el calendario
        $firstDayOfMonth = Carbon::create($this->year, $this->month, 1)->dayOfWeek;
        $daysInMonth = Carbon::create($this->year, $this->month, 1)->daysInMonth;

        return [
            'inspections' => $inspections,
            'year' => $this->year,
            'month' => $this->month,
            'firstDayOfMonth' => $firstDayOfMonth,
            'daysInMonth' => $daysInMonth,
        ];
    }
}