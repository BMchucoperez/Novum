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
                    ->description('Datos de identificación y clasificación de la embarcación')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nombre de la Embarcación')
                            ->formatStateUsing(fn ($state) => "<div style='font-size: 15px; font-weight: 700; color: #2E75B6;'>{$state}</div>")
                            ->html(),
                        Infolists\Components\TextEntry::make('registration_number')
                            ->label('Número de Matrícula')
                            ->formatStateUsing(fn ($state) => "<div style='font-size: 15px; font-weight: 700; color: #1f1f1f;'>{$state}</div>")
                            ->html(),
                        Infolists\Components\TextEntry::make('serviceType.name')
                            ->label('Tipo de Servicio')
                            ->formatStateUsing(fn ($state) => "<span style='display: inline-block; padding: 6px 12px; background: #dbeafe; color: #1e40af; border-radius: 4px; font-weight: 600; font-size: 13px;'>{$state}</span>")
                            ->html(),
                        Infolists\Components\TextEntry::make('navigationType.name')
                            ->label('Tipo de Navegación')
                            ->formatStateUsing(fn ($state) => "<span style='display: inline-block; padding: 6px 12px; background: #f0fdf4; color: #166534; border-radius: 4px; font-weight: 600; font-size: 13px;'>{$state}</span>")
                            ->html(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Embarcaciones Asociadas')
                    ->description('Embarcaciones que viajan en convoy con esta nave')
                    ->icon('heroicon-o-link')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('associatedVessels')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('associatedVessel.name')
                                    ->label('Nombre')
                                    ->columnSpanFull()
                                    ->formatStateUsing(fn ($state) => "
                                        <div style='background: #f8f9fa; border-left: 4px solid #10b981; padding: 12px 16px; border-radius: 6px; margin-bottom: 8px;'>
                                            <div style='font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 4px;'>🚢 Nombre</div>
                                            <div style='font-size: 14px; font-weight: 700; color: #2E75B6;'>{$state}</div>
                                        </div>
                                    ")
                                    ->html(),
                                Infolists\Components\TextEntry::make('associatedVessel.registration_number')
                                    ->label('Matrícula')
                                    ->columnSpanFull()
                                    ->formatStateUsing(fn ($state) => "
                                        <div style='background: #f8f9fa; border-left: 4px solid #10b981; padding: 12px 16px; border-radius: 6px;'>
                                            <div style='font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 4px;'>🔖 Matrícula</div>
                                            <div style='font-size: 14px; font-weight: 700; color: #1f1f1f;'>{$state}</div>
                                        </div>
                                    ")
                                    ->html(),
                            ])
                            ->columns(1)
                            ->columnSpanFull()
                            ->placeholder('📭 No hay embarcaciones asociadas'),
                    ])
                    ->collapsible(),

                Infolists\Components\Section::make('Propietario y Usuario')
                    ->description('Información de propiedad y gestión de la embarcación')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Infolists\Components\TextEntry::make('owner.name')
                            ->label('Propietario / Armador')
                            ->formatStateUsing(fn ($state) => "
                                <div style='background: #f0fdf4; border-left: 4px solid #10b981; padding: 12px 16px; border-radius: 6px;'>
                                    <div style='font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 4px;'>👤 Propietario</div>
                                    <div style='font-size: 14px; font-weight: 700; color: #1f1f1f;'>{$state}</div>
                                </div>
                            ")
                            ->html(),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Usuario Asignado')
                            ->formatStateUsing(fn ($state) => $state ? "
                                <div style='background: #f3e8ff; border-left: 4px solid #a855f7; padding: 12px 16px; border-radius: 6px;'>
                                    <div style='font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 4px;'>👨‍💼 Usuario</div>
                                    <div style='font-size: 14px; font-weight: 700; color: #1f1f1f;'>{$state}</div>
                                </div>
                            " : "
                                <div style='background: #f5f5f5; border-left: 4px solid #d1d5db; padding: 12px 16px; border-radius: 6px;'>
                                    <div style='font-size: 12px; color: #999; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 4px;'>👨‍💼 Usuario</div>
                                    <div style='font-size: 14px; font-weight: 700; color: #999;'>Sin asignar</div>
                                </div>
                            ")
                            ->html(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Características Técnicas')
                    ->description('Especificaciones y dimensiones de la embarcación')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Infolists\Components\TextEntry::make('construction_year')
                            ->label('Año de Construcción')
                            ->formatStateUsing(fn ($state) => "
                                <div style='text-align: center; padding: 12px 8px;'>
                                    <div style='font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 4px;'>📅</div>
                                    <div style='font-size: 16px; font-weight: 700; color: #2E75B6;'>{$state}</div>
                                </div>
                            ")
                            ->html(),
                        Infolists\Components\TextEntry::make('shipyard.name')
                            ->label('Astillero')
                            ->formatStateUsing(fn ($state) => "
                                <div style='background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px 16px; border-radius: 6px;'>
                                    <div style='font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 4px;'>🏗️ Astillero</div>
                                    <div style='font-size: 14px; font-weight: 700; color: #1f1f1f;'>{$state}</div>
                                </div>
                            ")
                            ->html(),
                        Infolists\Components\TextEntry::make('length')
                            ->label('Eslora')
                            ->formatStateUsing(fn ($state) => "
                                <div style='text-align: center; padding: 12px 8px;'>
                                    <div style='font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 4px;'>📏</div>
                                    <div style='font-size: 16px; font-weight: 700; color: #10b981;'>{$state} <span style=\"font-size: 12px; color: #999;\">m</span></div>
                                </div>
                            ")
                            ->html(),
                        Infolists\Components\TextEntry::make('beam')
                            ->label('Manga')
                            ->formatStateUsing(fn ($state) => "
                                <div style='text-align: center; padding: 12px 8px;'>
                                    <div style='font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 4px;'>📐</div>
                                    <div style='font-size: 16px; font-weight: 700; color: #f59e0b;'>{$state} <span style=\"font-size: 12px; color: #999;\">m</span></div>
                                </div>
                            ")
                            ->html(),
                        Infolists\Components\TextEntry::make('depth')
                            ->label('Puntal')
                            ->formatStateUsing(fn ($state) => "
                                <div style='text-align: center; padding: 12px 8px;'>
                                    <div style='font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 4px;'>📏</div>
                                    <div style='font-size: 16px; font-weight: 700; color: #8b5cf6;'>{$state} <span style=\"font-size: 12px; color: #999;\">m</span></div>
                                </div>
                            ")
                            ->html(),
                        Infolists\Components\TextEntry::make('gross_tonnage')
                            ->label('Arqueo Bruto')
                            ->formatStateUsing(fn ($state) => "
                                <div style='text-align: center; padding: 12px 8px;'>
                                    <div style='font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 4px;'>⚖️</div>
                                    <div style='font-size: 16px; font-weight: 700; color: #06b6d4;'>{$state} <span style=\"font-size: 12px; color: #999;\">ton</span></div>
                                </div>
                            ")
                            ->html(),
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
