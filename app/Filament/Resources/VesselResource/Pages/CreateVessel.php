<?php

namespace App\Filament\Resources\VesselResource\Pages;

use App\Filament\Resources\VesselResource;
use App\Models\VesselAssociation;
use App\Models\VesselDocument;
use App\Models\VesselDocumentType;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateVessel extends CreateRecord
{
    protected static string $resource = VesselResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        Log::info('CreateVessel: Iniciando procesamiento afterCreate', [
            'vessel_id' => $this->record->id,
            'vessel_name' => $this->record->name,
        ]);

        $this->handleVesselAssociations();
        $this->handleAllDocuments();

        Log::info('CreateVessel: Procesamiento afterCreate completado', [
            'vessel_id' => $this->record->id,
        ]);
    }


    protected function handleAllDocuments(): void
    {
        Log::info('CreateVessel: Iniciando procesamiento de TODOS los documentos', [
            'vessel_id' => $this->record->id,
            'form_data_keys' => array_keys($this->data),
        ]);

        // Procesar documentos de BANDEIRA E APOLICES
        $this->handleBandeiraApolicesDocuments();

        // Procesar documentos de SISTEMA DE GESTÃO
        $this->handleSistemaGestaoDocuments();

        // Procesar documentos exclusivos por tipo
        $this->handleExclusiveDocuments();
    }

    protected function handleBandeiraApolicesDocuments(): void
    {
        Log::info('CreateVessel: Procesando documentos Bandeira e Apólices');

        $bandeiraDocuments = VesselDocumentType::getBandeiraApolicesDocuments();
        $category = 'bandeira_apolices';

        foreach ($bandeiraDocuments as $documentType => $documentName) {
            $fieldName = "document_{$documentType}";
            $files = $this->data[$fieldName] ?? [];

            Log::info("CreateVessel: Procesando documento {$fieldName}", [
                'document_type' => $documentType,
                'document_name' => $documentName,
                'files_count' => count($files),
                'has_files' => !empty($files),
            ]);

            if (!empty($files)) {
                foreach ($files as $file) {
                    if ($file) {
                        Log::info("CreateVessel: Llamando handleDocumentUpload para {$fieldName}");
                        VesselResource::handleDocumentUpload(
                            $file,
                            $this->record,
                            $documentType,
                            $category,
                            $documentName
                        );
                    }
                }
            }
        }
    }

    protected function handleSistemaGestaoDocuments(): void
    {
        Log::info('CreateVessel: Procesando documentos Sistema de Gestão');

        $sistemaDocuments = VesselDocumentType::getSistemaGestaoDocuments();
        $category = 'sistema_gestao';

        foreach ($sistemaDocuments as $documentType => $documentName) {
            $fieldName = "document_{$documentType}";
            $files = $this->data[$fieldName] ?? [];

            Log::info("CreateVessel: Procesando documento {$fieldName}", [
                'document_type' => $documentType,
                'document_name' => $documentName,
                'files_count' => count($files),
                'has_files' => !empty($files),
            ]);

            if (!empty($files)) {
                foreach ($files as $file) {
                    if ($file) {
                        Log::info("CreateVessel: Llamando handleDocumentUpload para {$fieldName}");
                        VesselResource::handleDocumentUpload(
                            $file,
                            $this->record,
                            $documentType,
                            $category,
                            $documentName
                        );
                    }
                }
            }
        }
    }

    protected function handleExclusiveDocuments(): void
    {
        Log::info('CreateVessel: Procesando documentos exclusivos');

        $documentMapping = [
            // Documentos exclusivos para Barcazas
            'document_barcaza_declaracao_conformidade_transporte_petroleo' => [
                'type' => 'barcaza_declaracao_conformidade_transporte_petroleo',
                'category' => 'barcaza_exclusive',
                'name' => 'Declaração de conformidade para transporte de petróleo'
            ],
            
            // Documentos exclusivos para Empujadores
            'document_empujador_cartao_tripulacao_seguranca_cts' => [
                'type' => 'empujador_cartao_tripulacao_seguranca_cts',
                'category' => 'empujador_exclusive',
                'name' => 'Cartão de tripulação de segurança (CTS)'
            ],
            'document_empujador_licenca_estacao_navio' => [
                'type' => 'empujador_licenca_estacao_navio',
                'category' => 'empujador_exclusive',
                'name' => 'Licença de estação de navio'
            ],
            
            // Documentos exclusivos para Motochatas
            'document_motochata_documento_especial_1' => [
                'type' => 'motochata_documento_especial_1',
                'category' => 'motochata_exclusive',
                'name' => 'Documento especial motochata 1'
            ],
            'document_motochata_documento_especial_2' => [
                'type' => 'motochata_documento_especial_2',
                'category' => 'motochata_exclusive',
                'name' => 'Documento especial motochata 2'
            ],
        ];

        foreach ($documentMapping as $fieldName => $docInfo) {
            $files = $this->data[$fieldName] ?? [];

            Log::info("CreateVessel: Procesando documento exclusivo {$fieldName}", [
                'document_type' => $docInfo['type'],
                'document_name' => $docInfo['name'],
                'category' => $docInfo['category'],
                'files_count' => count($files),
                'has_files' => !empty($files),
            ]);

            if (!empty($files)) {
                foreach ($files as $file) {
                    if ($file) {
                        Log::info("CreateVessel: Llamando handleDocumentUpload para documento exclusivo {$fieldName}");
                        VesselResource::handleDocumentUpload(
                            $file,
                            $this->record,
                            $docInfo['type'],
                            $docInfo['category'],
                            $docInfo['name']
                        );
                    }
                }
            }
        }

        Log::info('CreateVessel: Procesamiento de todos los documentos completado');
    }

    protected function handleVesselAssociations(): void
    {
        $associatedVessels = $this->data['associated_vessels'] ?? [];

        Log::info('CreateVessel: Procesando asociaciones de embarcaciones', [
            'associated_vessels' => $associatedVessels,
            'count' => count($associatedVessels),
        ]);

        if (!empty($associatedVessels)) {
            foreach ($associatedVessels as $associatedVesselId) {
                VesselAssociation::create([
                    'main_vessel_id' => $this->record->id,
                    'associated_vessel_id' => $associatedVesselId,
                ]);

                Log::info('CreateVessel: Asociación creada', [
                    'main_vessel_id' => $this->record->id,
                    'associated_vessel_id' => $associatedVesselId,
                ]);
            }
        }
    }
}
