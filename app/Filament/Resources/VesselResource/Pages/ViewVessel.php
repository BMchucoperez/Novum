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
                    ->description('Estadísticas completas y resumen de documentos cargados')
                    ->schema([
                        Infolists\Components\Grid::make(2)->schema([
                            // Primera fila - Estadísticas principales
                            Infolists\Components\Grid::make(4)->schema([
                                Infolists\Components\TextEntry::make('total_documents')
                                    ->label('Total Documentos')
                                    ->state(function ($record) {
                                        return $record->vesselDocuments()->count();
                                    })
                                    ->formatStateUsing(fn ($state) => "
                                        <div class='text-center p-3 bg-blue-50 rounded-lg border border-blue-200'>
                                            <div class='text-2xl font-bold text-blue-700'>{$state}</div>
                                            <div class='text-xs text-blue-600 mt-1'>Documentos</div>
                                        </div>
                                    ")
                                    ->html(),

                                Infolists\Components\TextEntry::make('valid_documents')
                                    ->label('Documentos Válidos')
                                    ->state(function ($record) {
                                        return $record->vesselDocuments()->valid()->count();
                                    })
                                    ->formatStateUsing(fn ($state) => "
                                        <div class='text-center p-3 bg-green-50 rounded-lg border border-green-200'>
                                            <div class='text-2xl font-bold text-green-700'>{$state}</div>
                                            <div class='text-xs text-green-600 mt-1'>Válidos</div>
                                        </div>
                                    ")
                                    ->html(),

                                Infolists\Components\TextEntry::make('expired_documents')
                                    ->label('Documentos Vencidos')
                                    ->state(function ($record) {
                                        return $record->vesselDocuments()->expired()->count();
                                    })
                                    ->formatStateUsing(function ($state) {
                                        $bgColor = $state > 0 ? 'bg-red-50 border-red-200' : 'bg-gray-50 border-gray-200';
                                        $textColor = $state > 0 ? 'text-red-700' : 'text-gray-600';
                                        $subTextColor = $state > 0 ? 'text-red-600' : 'text-gray-500';
                                        return "
                                            <div class='text-center p-3 {$bgColor} rounded-lg border'>
                                                <div class='text-2xl font-bold {$textColor}'>{$state}</div>
                                                <div class='text-xs {$subTextColor} mt-1'>Vencidos</div>
                                            </div>
                                        ";
                                    })
                                    ->html(),

                                Infolists\Components\TextEntry::make('completeness')
                                    ->label('Completitud')
                                    ->state(function ($record) {
                                        return $record->getDocumentCompleteness();
                                    })
                                    ->formatStateUsing(function ($state) {
                                        $colors = match(true) {
                                            $state >= 80 => ['bg' => 'bg-green-50 border-green-200', 'text' => 'text-green-700', 'sub' => 'text-green-600'],
                                            $state >= 50 => ['bg' => 'bg-yellow-50 border-yellow-200', 'text' => 'text-yellow-700', 'sub' => 'text-yellow-600'],
                                            default => ['bg' => 'bg-red-50 border-red-200', 'text' => 'text-red-700', 'sub' => 'text-red-600']
                                        };
                                        return "
                                            <div class='text-center p-3 {$colors['bg']} rounded-lg border'>
                                                <div class='text-2xl font-bold {$colors['text']}'>{$state}%</div>
                                                <div class='text-xs {$colors['sub']} mt-1'>Completitud</div>
                                            </div>
                                        ";
                                    })
                                    ->html(),
                            ])
                            ->columnSpan(2),
                        ]),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(false),

                Infolists\Components\Section::make('Documentos Anexos')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('vesselDocuments')
                            ->schema([
                                Infolists\Components\TextEntry::make('document_name')
                                    ->label('Documento'),
                                Infolists\Components\TextEntry::make('download_button')
                                    ->label('Descargar')
                                    ->state(function ($record) {
                                        $filePath = storage_path('app/public/' . $record->file_path);
                                        if (file_exists($filePath)) {
                                            $url = \Illuminate\Support\Facades\Storage::disk('public')->url($record->file_path);
                                            return '<a href="' . $url . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Descargar</a>';
                                        }
                                        return 'No disponible';
                                    })
                                    ->html(),
                            ])
                            ->columns(2)
                            ->placeholder('No hay documentos'),
                    ]),
            ]);
    }
}
