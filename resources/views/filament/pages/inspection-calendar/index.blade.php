<x-filament-panels::page>
    <div class="flex flex-col gap-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h2 class="text-2xl font-bold text-gray-900">
                {{ \Carbon\Carbon::create($year, $month, 1)->locale('es')->isoFormat('MMMM YYYY') }}
            </h2>
            <div class="flex gap-2">
                <x-filament::button icon="heroicon-o-chevron-left" wire:click="previousMonth">
                    Mes Anterior
                </x-filament::button>
                <x-filament::button color="gray" wire:click="goToToday">
                    Hoy
                </x-filament::button>
                <x-filament::button icon="heroicon-o-chevron-right" icon-position="after" wire:click="nextMonth">
                    Próximo Mes
                </x-filament::button>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
                @foreach(['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'] as $day)
                    <div class="p-3 text-center font-semibold text-gray-700 text-sm">
                        {{ substr($day, 0, 3) }}
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-7 gap-px bg-gray-200">
                @for($i = 0; $i < $firstDayOfMonth; $i++)
                    <div class="p-2 h-40 bg-white"></div>
                @endfor

                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $date = \Carbon\Carbon::create($year, $month, $day);
                        $isToday = $date->isToday();
                        $dayInspections = $inspections->filter(fn($inspection) => \Carbon\Carbon::parse($inspection['start'])->isSameDay($date));
                    @endphp

                    <div class="p-2 h-40 bg-white flex flex-col {{ $isToday ? 'bg-blue-50 border-2 border-blue-300' : '' }}">
                        <div class="font-semibold text-gray-800 {{ $isToday ? 'text-blue-600' : '' }}">
                            {{ $day }}
                        </div>
                        <div class="mt-1 space-y-1 overflow-y-auto flex-grow">
                            @forelse($dayInspections as $inspection)
                                <div class="text-xs p-1 rounded truncate cursor-pointer hover:opacity-90 hover:scale-105 transition-all duration-200 shadow-sm border border-white" {!! $inspection['status_color'] !!}
                                     title="{{ $inspection['title'] }} - {{ $inspection['vessel_name'] }} ({{ $inspection['status_label'] }}) - Clic para ver detalles"
                                     wire:click="showInspectionDetails({{ $inspection['id'] }})">
                                    <div class="font-medium truncate">{{ $inspection['title'] }}</div>
                                    <div class="truncate opacity-90">{{ $inspection['vessel_name'] }}</div>
                                    <div class="text-xs opacity-75 mt-1">
                                        <span class="inline-flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Ver detalles
                                        </span>
                                    </div>
                                </div>
                            @empty
                                @if($isToday)
                                    <div class="text-xs text-gray-400 italic">
                                        Hoy
                                    </div>
                                @endif
                            @endforelse
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <h3 class="text-lg font-semibold mb-4 text-gray-900">Leyenda de Estados</h3>
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded mr-2 border shadow-sm" style="background-color: #3b82f6; border-color: #2563eb;"></div>
                    <span class="text-gray-700 font-medium">Programada</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded mr-2 border shadow-sm" style="background-color: #22c55e; border-color: #16a34a;"></div>
                    <span class="text-gray-700 font-medium">Completada</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded mr-2 border shadow-sm" style="background-color: #ef4444; border-color: #dc2626;"></div>
                    <span class="text-gray-700 font-medium">Cancelada</span>
                </div>
            </div>
        </div>

        @if($inspections->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                <x-heroicon-o-calendar class="h-16 w-16 mx-auto text-gray-400" />
                <h3 class="mt-4 text-lg font-medium text-gray-900">No hay inspecciones programadas</h3>
                <p class="mt-2 text-gray-500">
                    No se encontraron inspecciones para {{ \Carbon\Carbon::create($year, $month, 1)->locale('es')->isoFormat('MMMM YYYY') }}.
                </p>
                <div class="mt-4">
                    <x-filament::button 
                        href="{{ route('filament.admin.resources.inspection-schedules.create') }}" 
                        icon="heroicon-o-plus">
                        Programar nueva inspección
                    </x-filament::button>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>