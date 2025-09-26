<?php

namespace App\Filament\Resources\VesselResource\Pages;

use App\Filament\Resources\VesselResource;
use App\Models\VesselDocument;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewVessel extends ViewRecord
{
    protected static string $resource = VesselResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información General')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nombre'),
                        Infolists\Components\TextEntry::make('registration_number')
                            ->label('Número de Matrícula'),
                        Infolists\Components\TextEntry::make('serviceType.name')
                            ->label('Tipo de Servicio'),
                        Infolists\Components\TextEntry::make('navigationType.name')
                            ->label('Tipo de Navegación'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Embarcaciones Asociadas')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('associatedVessels')
                            ->label('Embarcaciones Asociadas')
                            ->schema([
                                Infolists\Components\TextEntry::make('associatedVessel.name')
                                    ->label('Nombre'),
                                Infolists\Components\TextEntry::make('associatedVessel.registration_number')
                                    ->label('Matrícula'),
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->placeholder('No hay embarcaciones asociadas'),
                    ])
                    ->collapsible(),

                Infolists\Components\Section::make('Propietario y Usuario')
                    ->schema([
                        Infolists\Components\TextEntry::make('owner.name')
                            ->label('Propietario'),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Usuario Asignado'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Características Técnicas')
                    ->schema([
                        Infolists\Components\TextEntry::make('construction_year')
                            ->label('Año de Construcción'),
                        Infolists\Components\TextEntry::make('shipyard.name')
                            ->label('Astillero'),
                        Infolists\Components\TextEntry::make('length')
                            ->label('Eslora')
                            ->suffix(' m'),
                        Infolists\Components\TextEntry::make('beam')
                            ->label('Manga')
                            ->suffix(' m'),
                        Infolists\Components\TextEntry::make('depth')
                            ->label('Puntal')
                            ->suffix(' m'),
                        Infolists\Components\TextEntry::make('gross_tonnage')
                            ->label('Arqueo Bruto')
                            ->suffix(' ton'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Resumen de Documentos')
                    ->description('Estadísticas y resumen de documentos cargados')
                    ->schema([
                        Infolists\Components\Grid::make(4)->schema([
                            Infolists\Components\TextEntry::make('total_documents')
                                ->label('Total Documentos')
                                ->state(function ($record) {
                                    return $record->vesselDocuments()->count();
                                })
                                ->formatStateUsing(fn ($state) => "<span class='text-xl font-bold text-blue-600'>{$state}</span>")
                                ->html()
                                ->extraAttributes(['class' => 'text-center']),

                            Infolists\Components\TextEntry::make('valid_documents')
                                ->label('Documentos Válidos')
                                ->state(function ($record) {
                                    return $record->vesselDocuments()->valid()->count();
                                })
                                ->formatStateUsing(fn ($state) => "<span class='text-xl font-bold text-green-600'>{$state}</span>")
                                ->html()
                                ->extraAttributes(['class' => 'text-center']),

                            Infolists\Components\TextEntry::make('expired_documents')
                                ->label('Documentos Vencidos')
                                ->state(function ($record) {
                                    return $record->vesselDocuments()->expired()->count();
                                })
                                ->formatStateUsing(function ($state) {
                                    $color = $state > 0 ? 'text-red-600' : 'text-gray-600';
                                    return "<span class='text-xl font-bold {$color}'>{$state}</span>";
                                })
                                ->html()
                                ->extraAttributes(['class' => 'text-center']),

                            Infolists\Components\TextEntry::make('completeness')
                                ->label('Completitud')
                                ->state(function ($record) {
                                    return $record->getDocumentCompleteness();
                                })
                                ->formatStateUsing(function ($state) {
                                    $color = $state >= 80 ? 'text-green-600' : ($state >= 50 ? 'text-yellow-600' : 'text-red-600');
                                    return "<span class='text-xl font-bold {$color}'>{$state}%</span>";
                                })
                                ->html()
                                ->extraAttributes(['class' => 'text-center']),
                        ]),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Documentos Anexos')
                    ->description(function ($record) {
                        $count = $record->vesselDocuments()->count();
                        return "Actualmente hay {$count} documento" . ($count !== 1 ? 's' : '') . " cargados. Desplázate para ver todos los documentos.";
                    })
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('vesselDocuments')
                            ->label('Documentos')
                            ->schema([
                                // Primera fila: Información principal
                                Infolists\Components\Grid::make(4)->schema([
                                    Infolists\Components\TextEntry::make('document_name')
                                        ->label('Documento')
                                        ->weight('bold')
                                        ->columnSpan(2),
                                    
                                    Infolists\Components\TextEntry::make('document_category')
                                        ->label('Categoría')
                                        ->formatStateUsing(fn (string $state): string => match($state) {
                                            'bandeira_apolices' => 'Bandeira e Apólices',
                                            'sistema_gestao' => 'Sistema de Gestão',
                                            'barcaza_exclusive' => 'Barcaza Exclusivo',
                                            'empujador_exclusive' => 'Empujador Exclusivo',
                                            'motochata_exclusive' => 'Motochata Exclusivo',
                                            default => $state,
                                        })
                                        ->badge()
                                        ->color(fn (string $state): string => match($state) {
                                            'bandeira_apolices' => 'primary',
                                            'sistema_gestao' => 'success',
                                            'barcaza_exclusive' => 'warning',
                                            'empujador_exclusive' => 'info',
                                            'motochata_exclusive' => 'secondary',
                                            default => 'gray',
                                        }),

                                    Infolists\Components\TextEntry::make('status')
                                        ->label('Estado')
                                        ->state(function (VesselDocument $record): string {
                                            return $record->getStatusText();
                                        })
                                        ->badge()
                                        ->color(fn (VesselDocument $record): string => $record->getStatusColor()),
                                ]),
                                
                                // Segunda fila: Detalles del archivo y acciones
                                Infolists\Components\Grid::make(4)->schema([
                                    Infolists\Components\TextEntry::make('file_name')
                                        ->label('Nombre del Archivo')
                                        ->limit(40)
                                        ->tooltip(function (VesselDocument $record) {
                                            return $record->file_name ?? 'Sin nombre';
                                        }),
                                    
                                    Infolists\Components\TextEntry::make('file_size')
                                        ->label('Tamaño')
                                        ->formatStateUsing(fn (int $state): string => $state ? number_format($state / 1024 / 1024, 2) . ' MB' : ''),
                                    
                                    Infolists\Components\TextEntry::make('uploaded_at')
                                        ->label('Fecha de Subida')
                                        ->dateTime('d/m/Y H:i'),
                                    
                                    Infolists\Components\TextEntry::make('download_action')
                                        ->label('Acciones')
                                        ->state('')
                                        ->formatStateUsing(function ($state, VesselDocument $record) {
                                            if (!$record || !$record->file_path) {
                                                return '<span class="text-gray-400">No disponible</span>';
                                            }
                                            
                                            $url = \Illuminate\Support\Facades\Storage::url($record->file_path);
                                            return '<a href="' . $url . '" 
                                                    target="_blank" 
                                                    class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                        Descargar
                                                    </a>';
                                        })
                                        ->html(),
                                ]),
                            ])
                            ->columns(1)
                            ->columnSpanFull()
                            ->placeholder('No hay documentos cargados')
                            ->extraAttributes(['class' => 'divide-y divide-gray-200']),
                    ])
                    ->collapsible()
                    ->collapsed(false)
                    ->extraAttributes(['class' => 'max-h-96 overflow-y-auto']),
            ]);
    }
}
