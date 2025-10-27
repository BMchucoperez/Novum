<?php

namespace App\Filament\Resources\VesselResource\Pages;

use App\Filament\Resources\VesselResource;
use App\Models\VesselDocument;
use App\Models\VesselDocumentType;
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

    /**
     * Traducir nombres de documentos de portugués a español
     */
    private function translateDocumentName(string $documentName): string
    {
        $translations = [
            // BANDEIRA E APÓLICES
            'Certificado nacional de arqueação' => 'Certificado de Arqueo',
            'Certificado nacional de borda livre para a navegação interior' => 'Certificado de Línea Máxima de Carga',
            'Provisão de registro da propriedade marítima' => 'Certificado de Matrícula',
            'Certificado de segurança de navegação' => 'Certificado Nacional de Seguridad para naves fluviales',
            'Licença de operação - IPAAM' => 'Certificado Nacional de Aprobación del Plan de Emergencia de a Bordo',
            'Autorização de ANP' => 'Permiso de Operaciones para Prestar Servicio de Transporte Fluvial',
            'Autorização de ANTAQ' => 'Certificado de Seguro de Responsabilidad Civil por Daños',
            'Autorização ambiental para o transporte interestadual de produtos perigosos - IBAMA' => 'Certificado de Aptitud para el Transporte Marítimo de Mercancías Peligrosas',
            'Certificado de regularidade - IBAMA' => 'Certificado de regularidade - IBAMA',
            'Certificado de registro de armador (CRA)' => 'Certificado de Cumplimiento Relativo al Doble Casco',
            'Apólice de seguro P&I' => 'Póliza de Casco Marítimo P&I',

            // SISTEMA DE GESTIÓN
            'Livro de oleo' => 'Libro de Aceite',
            'Plano de segurança' => 'Plano de Seguridad',
            'Plano de arranjo geral' => 'Plano de Disposición General',
            'Plano de rede de carga e descarga' => 'Plano del Sistema de Carga y Descarga',
            'Plano de capacidade de tanques' => 'Plano de Disposición de Tanques',
            'Teste de Opacidade' => 'Prueba de Opacidad',
            'Certificado de teste pneumático dos tanques de armazenamento de óleo' => 'Certificado de Prueba de Estanqueidad de los Tanques de Carga',
            'Certificado de Teste da rede de carga / descarga' => 'Certificado de Prueba Hidrostática del Sistema de Carga y Descarga',
            'Certificado de Teste da válvula de pressão e vácuo' => 'Certificado de Prueba de Válvulas de Presión y Vacío',
            'Plano de Emergência a Bordo para Poluição por Óleo - SOPEP' => 'Plan de Emergencia a Bordo para Casos de Derrame de Hidrocarburos',
            'Certificados de Teste Hidrostático e Manutenção para Extintores de Incêndio' => 'Certificados de Prueba Hidrostática y Mantenimiento de los Extintores',

            // DOCUMENTOS EXCLUSIVOS BARCAZA
            'Declaração de conformidade para transporte de petróleo' => 'Ficha de Registro Medio de Transporte Fluvial',

            // DOCUMENTOS EXCLUSIVOS EMPUJADOR
            'Cartão de tripulação de segurança (CTS)' => 'Certificado de Dotación Mínima',
            'Licença de estação de navio' => 'Permiso para Operar una Estación de Comunicación de Teleservicio Móvil',
            'Certificado de controle de Praga' => 'Certificado de Fumigación, Desinfección y Desratización',
            'Plano de incêndio' => 'Plano Contraincendio',
            'Operador técnico - DPA' => 'Operador técnico - DPA',
            'Crew List' => 'Crew List',
        ];

        return $translations[$documentName] ?? $documentName;
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
                                    ->label('Documento')
                                    ->formatStateUsing(function ($state) {
                                        $translated = $this->translateDocumentName($state);
                                        return "<strong>🇵🇹 {$state}</strong><br><small style='color: #666;'>🇪🇸 {$translated}</small>";
                                    })
                                    ->html(),
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
