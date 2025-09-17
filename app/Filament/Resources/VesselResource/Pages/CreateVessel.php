<?php

namespace App\Filament\Resources\VesselResource\Pages;

use App\Filament\Resources\VesselResource;
use App\Models\VesselAssociation;
use App\Models\VesselDocument;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVessel extends CreateRecord
{
    protected static string $resource = VesselResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $this->handleVesselAssociations();
        $this->handleExclusiveDocuments();
    }

    protected function handleVesselAssociations(): void
    {
        $associatedVessels = $this->data['associated_vessels'] ?? [];
        
        if (!empty($associatedVessels)) {
            foreach ($associatedVessels as $associatedVesselId) {
                VesselAssociation::create([
                    'main_vessel_id' => $this->record->id,
                    'associated_vessel_id' => $associatedVesselId,
                ]);
            }
        }
    }

    protected function handleExclusiveDocuments(): void
    {
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
            
            if (!empty($files)) {
                foreach ($files as $file) {
                    if ($file) {
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
    }
}
