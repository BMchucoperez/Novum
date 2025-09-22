<?php

namespace App\Filament\Pages;

use App\Models\InspectionSchedule;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Carbon;

class InspectionCalendar extends Page implements HasActions, HasForms, HasInfolists
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static string $view = 'filament.pages.inspection-calendar.index';

    protected static ?string $navigationGroup = 'Inspecciones';

    protected static ?string $navigationLabel = 'Calendario de Inspecciones';

    protected static ?int $navigationSort = 7;

    public $year;
    public $month;
    public $selectedInspection = null;

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

    public function showInspectionDetails($inspectionId): void
    {
        $this->selectedInspection = InspectionSchedule::with(['vessel.owner', 'statutoryCertificate'])->find($inspectionId);
        
        if ($this->selectedInspection) {
            $this->mountAction('viewInspection');
        }
    }

    public function viewInspectionAction(): Action
    {
        return Action::make('viewInspection')
            ->label('Detalles de la Inspección')
            ->modalHeading(fn () => $this->selectedInspection ? 'Detalles de la Inspección: ' . $this->selectedInspection->title : 'Detalles de la Inspección')
            ->modalDescription(fn () => $this->selectedInspection ? 'Información completa de la inspección programada' : '')
            ->modalContent(fn () => $this->selectedInspection ? $this->inspectionInfolist($this->selectedInspection)->render() : null)
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Cerrar')
            ->closeModalByClickingAway(false)
            ->icon('heroicon-o-eye');
    }

    protected function getActions(): array
    {
        return [
            $this->viewInspectionAction(),
        ];
    }

    public function inspectionInfolist(InspectionSchedule $inspection): Infolist
    {
        return Infolist::make()
            ->record($inspection)
            ->schema([
                Section::make('📋 Información General')
                    ->description('Datos básicos de la inspección programada')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('title')
                                    ->label('🏷️ Título')
                                    ->weight(FontWeight::Bold)
                                    ->color('primary'),
                                    
                                TextEntry::make('status')
                                    ->label('📊 Estado')
                                    ->formatStateUsing(fn (string $state): string => InspectionSchedule::getStatusLabel($state))
                                    ->badge()
                                    ->color(function (string $state): string {
                                        return match (strtolower($state)) {
                                            'scheduled', 'programada' => 'info',
                                            'completed', 'completada' => 'success',
                                            'cancelled', 'cancelada' => 'danger',
                                            default => 'gray',
                                        };
                                    }),
                            ]),
                            
                        TextEntry::make('description')
                            ->label('📝 Descripción')
                            ->placeholder('Sin descripción')
                            ->columnSpanFull(),
                    ]),
                    
                Section::make('🚢 Información de la Embarcación')
                    ->description('Datos de la embarcación a inspeccionar')
                    ->icon('heroicon-o-rectangle-stack')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('vessel.name')
                                    ->label('🚢 Embarcación')
                                    ->weight(FontWeight::Bold)
                                    ->color('primary'),
                                    
                                TextEntry::make('vessel.owner.name')
                                    ->label('🏢 Propietario')
                                    ->weight(FontWeight::Medium),
                            ]),
                            
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('vessel.registration_number')
                                    ->label('📋 Número de Registro')
                                    ->placeholder('No especificado'),
                                    
                                TextEntry::make('vessel.serviceType.name')
                                    ->label('⚙️ Tipo de Embarcación')
                                    ->placeholder('No especificado'),
                                    
                                TextEntry::make('vessel.construction_year')
                                    ->label('📅 Año de Construcción')
                                    ->placeholder('No especificado'),
                            ]),
                    ]),
                    
                Section::make('📅 Programación')
                    ->description('Fechas y horarios de la inspección')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('start_datetime')
                                    ->label('🕐 Fecha y Hora de Inicio')
                                    ->dateTime('d/m/Y H:i')
                                    ->weight(FontWeight::Bold)
                                    ->color('success'),
                                    
                                TextEntry::make('end_datetime')
                                    ->label('🕐 Fecha y Hora de Fin')
                                    ->dateTime('d/m/Y H:i')
                                    ->weight(FontWeight::Bold)
                                    ->color('warning'),
                            ]),
                            
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('location')
                                    ->label('📍 Ubicación')
                                    ->placeholder('No especificada'),
                                    
                                TextEntry::make('inspector_name')
                                    ->label('👷 Inspector Asignado')
                                    ->placeholder('No asignado'),
                            ]),
                    ]),
                    
                Section::make('📜 Certificado Estatutario')
                    ->description('Información del certificado relacionado (si aplica)')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('statutoryCertificate.certificate_type')
                                    ->label('📋 Tipo de Certificado')
                                    ->placeholder('No especificado'),
                                    
                                TextEntry::make('statutoryCertificate.certificate_number')
                                    ->label('🔢 Número de Certificado')
                                    ->placeholder('No especificado'),
                            ]),
                            
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('statutoryCertificate.issue_date')
                                    ->label('📅 Fecha de Emisión')
                                    ->date('d/m/Y')
                                    ->placeholder('No especificada'),
                                    
                                TextEntry::make('statutoryCertificate.expiry_date')
                                    ->label('📅 Fecha de Vencimiento')
                                    ->date('d/m/Y')
                                    ->placeholder('No especificada')
                                    ->color(function ($state) {
                                        if (!$state) return 'gray';
                                        $expiryDate = Carbon::parse($state);
                                        $now = now();
                                        
                                        if ($expiryDate->isPast()) {
                                            return 'danger';
                                        } elseif ($expiryDate->diffInDays($now) <= 30) {
                                            return 'warning';
                                        }
                                        return 'success';
                                    }),
                            ]),
                    ])
                    ->visible(function () use ($inspection) {
                        return $inspection->statutoryCertificate !== null;
                    }),
                    
                Section::make('ℹ️ Información Adicional')
                    ->description('Datos técnicos y de registro')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('📅 Creado el')
                                    ->dateTime('d/m/Y H:i')
                                    ->color('gray'),
                                    
                                TextEntry::make('updated_at')
                                    ->label('📅 Última actualización')
                                    ->dateTime('d/m/Y H:i')
                                    ->color('gray'),
                                    
                                TextEntry::make('id')
                                    ->label('🆔 ID de Inspección')
                                    ->color('gray'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
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