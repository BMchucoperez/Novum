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
                    <div class="p-2 h-32 bg-white"></div>
                @endfor

                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $date = \Carbon\Carbon::create($year, $month, $day);
                        $isToday = $date->isToday();
                        $dayInspections = $inspections->filter(fn($inspection) => \Carbon\Carbon::parse($inspection['start'])->isSameDay($date));
                    @endphp

                    <div class="p-2 h-32 bg-white flex flex-col {{ $isToday ? 'bg-blue-50 border-2 border-blue-300' : '' }}">
                        <div class="font-semibold text-gray-800 {{ $isToday ? 'text-blue-600' : '' }}">
                            {{ $day }}
                        </div>
                        <div class="mt-1 space-y-1 overflow-y-auto flex-grow">
                            @forelse($dayInspections as $inspection)
                                <div class="text-xs p-1 rounded truncate cursor-pointer hover:opacity-90 transition-opacity shadow-sm border border-white" {!! $inspection['status_color'] !!}
                                     title="{{ $inspection['title'] }} - {{ $inspection['vessel_name'] }} ({{ $inspection['status_label'] }})">
                                    <div class="font-medium truncate">{{ $inspection['title'] }}</div>
                                    <div class="truncate opacity-90">{{ $inspection['vessel_name'] }}</div>
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