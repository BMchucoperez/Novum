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
                    ->description('Documentos requeridos para operaciones marítimas legales')
                    ->icon('heroicon-o-document-arrow-down')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('vesselDocuments')
                            ->schema([
                                Infolists\Components\TextEntry::make('document_name')
                                    ->label('')
                                    ->columnSpanFull()
                                    ->formatStateUsing(function ($state) {
                                        $translated = $this->translateDocumentName($state);
                                        return "
                                            <div style='background: #f8f9fa; border-left: 4px solid #2E75B6; padding: 16px; border-radius: 6px; margin-bottom: 12px;'>
                                                <div style='display: flex; gap: 20px; align-items: flex-start;'>
                                                    <div style='flex: 1;'>
                                                        <div style='font-size: 13px; color: #666; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; font-weight: 600;'>🇵🇹 Portugués</div>
                                                        <div style='font-size: 14px; font-weight: 600; color: #1f1f1f; line-height: 1.4;'>{$state}</div>
                                                    </div>
                                                    <div style='flex: 1;'>
                                                        <div style='font-size: 13px; color: #666; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; font-weight: 600;'>🇪🇸 Español</div>
                                                        <div style='font-size: 14px; font-weight: 600; color: #2E75B6; line-height: 1.4;'>{$translated}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        ";
                                    })
                                    ->html(),
                                Infolists\Components\TextEntry::make('download_button')
                                    ->label('')
                                    ->columnSpanFull()
                                    ->state(function ($record) {
                                        $filePath = storage_path('app/public/' . $record->file_path);
                                        if (file_exists($filePath)) {
                                            $url = \Illuminate\Support\Facades\Storage::disk('public')->url($record->file_path);
                                            return '<a href="' . $url . '" target="_blank" style="display: inline-block; padding: 10px 20px; background: #10b981; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 13px; transition: all 0.2s ease; border: 2px solid #10b981;" onmouseover="this.style.background=\'#059669\'; this.style.transform=\'translateY(-2px)\'; this.style.boxShadow=\'0 4px 12px rgba(16, 185, 129, 0.3)\';" onmouseout="this.style.background=\'#10b981\'; this.style.transform=\'translateY(0)\'; this.style.boxShadow=\'none\';">📥 Descargar Documento</a>';
                                        }
                                        return '<span style="display: inline-block; padding: 10px 20px; background: #e5e7eb; color: #6b7280; border-radius: 6px; font-weight: 600; font-size: 13px;">⚠️ No disponible</span>';
                                    })
                                    ->html(),
                            ])
                            ->columns(1)
                            ->columnSpanFull()
                            ->placeholder('📭 No hay documentos anexados')
                            ->hiddenLabel(),
                    ]),
            ]);
    }
}
