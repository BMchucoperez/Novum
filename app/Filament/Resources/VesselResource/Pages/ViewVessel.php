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
