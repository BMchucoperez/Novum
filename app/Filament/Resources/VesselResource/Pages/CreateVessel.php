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

    protected function beforeCreate(): void
    {
        Log::info('📝 ========== FORM DATA SUBMITTED - CREATE ==========', [
            'action' => 'CREATE_VESSEL',
            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email,
            'timestamp' => now()->toDateTimeString(),
            'request_ip' => request()->ip(),
            'request_user_agent' => request()->userAgent(),
            'form_data_count' => count($this->data),
            'memory_usage' => memory_get_usage(true),
        ]);

        // Log todos los datos del formulario principales
        $mainFields = [
            'name', 'imo_number', 'vessel_type', 'flag_country', 'call_sign',
            'gross_tonnage', 'net_tonnage', 'length_overall', 'beam', 'depth',
            'built_year', 'shipyard_id', 'owner_id', 'port_of_registry',
            'service_type_id', 'navigation_type_id', 'associated_vessels'
        ];

        $mainData = [];
        foreach ($mainFields as $field) {
            if (isset($this->data[$field])) {
                $mainData[$field] = $this->data[$field];
            }
        }

        Log::info('🚢 VESSEL DATA - MAIN FIELDS', $mainData);

        // Log todos los campos de documentos
        $documentFields = array_filter($this->data, function($key) {
            return str_starts_with($key, 'document_');
        }, ARRAY_FILTER_USE_KEY);

        Log::info('📄 VESSEL DATA - DOCUMENT FIELDS COMPLETE', [
            'document_fields_count' => count($documentFields),
            'document_fields_detail' => array_map(function($files, $key) {
                $fileDetails = [];
                if (is_array($files)) {
                    foreach ($files as $index => $file) {
                        $fileDetails[$index] = [
                            'type' => gettype($file),
                            'is_object' => is_object($file),
                            'class' => is_object($file) ? get_class($file) : null,
                            'original_name' => is_object($file) && method_exists($file, 'getClientOriginalName') ? $file->getClientOriginalName() : null,
                            'size' => is_object($file) && method_exists($file, 'getSize') ? $file->getSize() : null,
                            'is_string' => is_string($file),
                            'string_value' => is_string($file) ? $file : null,
                        ];
                    }
                } else {
                    $fileDetails = [
                        'type' => gettype($files),
                        'is_object' => is_object($files),
                        'class' => is_object($files) ? get_class($files) : null,
                        'original_name' => is_object($files) && method_exists($files, 'getClientOriginalName') ? $files->getClientOriginalName() : null,
                        'size' => is_object($files) && method_exists($files, 'getSize') ? $files->getSize() : null,
                        'is_string' => is_string($files),
                        'string_value' => is_string($files) ? $files : null,
                    ];
                }

                return [
                    'field' => $key,
                    'has_content' => !empty($files),
                    'files_count' => is_array($files) ? count($files) : (empty($files) ? 0 : 1),
                    'files_detail' => $fileDetails
                ];
            }, $documentFields, array_keys($documentFields))
        ]);

        // Log otros campos que no sean principales ni documentos
        $otherFields = array_diff_key($this->data, array_flip($mainFields), $documentFields);
        if (!empty($otherFields)) {
            Log::info('🔧 VESSEL DATA - OTHER FIELDS', [
                'other_fields' => $otherFields
            ]);
        }
    }

    protected function afterCreate(): void
    {
        Log::info('🚢 ========== CREATEVESSEL AFTERCREATE INICIADO ==========', [
            'vessel_id' => $this->record->id,
            'vessel_name' => $this->record->name,
            'vessel_type' => $this->record->vessel_type,
            'form_data_count' => count($this->data),
            'memory_usage' => memory_get_usage(true),
            'timestamp' => now()->toDateTimeString(),
            'user_id' => auth()->id(),
            'request_id' => request()->ip() . '_' . time(),
        ]);

        // Log completo de todos los datos del formulario
        $documentFields = array_filter($this->data, function($key) {
            return str_starts_with($key, 'document_');
        }, ARRAY_FILTER_USE_KEY);

        Log::info('📄 FORM DATA COMPLETO - DOCUMENTOS', [
            'vessel_id' => $this->record->id,
            'document_fields_count' => count($documentFields),
            'document_fields' => array_map(function($files, $key) {
                return [
                    'field' => $key,
                    'files_count' => is_array($files) ? count($files) : (empty($files) ? 0 : 1),
                    'has_content' => !empty($files),
                    'type' => gettype($files)
                ];
            }, $documentFields, array_keys($documentFields)),
            'associated_vessels' => $this->data['associated_vessels'] ?? [],
            'form_data_keys' => array_keys($this->data),
        ]);

        try {
            $this->handleVesselAssociations();
            $this->handleAllDocuments();

            Log::info('✅ ========== CREATEVESSEL AFTERCREATE COMPLETADO EXITOSAMENTE ==========', [
                'vessel_id' => $this->record->id,
                'vessel_name' => $this->record->name,
                'processing_time' => 'completed_at_' . now()->toDateTimeString(),
                'final_memory_usage' => memory_get_usage(true),
            ]);

        } catch (\Exception $e) {
            Log::error('❌ ========== ERROR EN CREATEVESSEL AFTERCREATE ==========', [
                'vessel_id' => $this->record->id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'form_data_keys' => array_keys($this->data),
            ]);
            throw $e;
        }
    }


    protected function handleAllDocuments(): void
    {
        $startTime = microtime(true);

        Log::info('📁 ========== INICIANDO PROCESAMIENTO COMPLETO DE DOCUMENTOS ==========', [
            'vessel_id' => $this->record->id,
            'vessel_name' => $this->record->name,
            'form_data_keys_count' => count($this->data),
            'document_fields' => array_filter(array_keys($this->data), function($key) {
                return str_starts_with($key, 'document_');
            }),
            'start_memory' => memory_get_usage(true),
            'start_time' => now()->toDateTimeString(),
        ]);

        try {
            // Procesar documentos de BANDEIRA E APOLICES
            Log::info('🇧🇷 Iniciando procesamiento BANDEIRA E APOLICES', ['vessel_id' => $this->record->id]);
            $this->handleBandeiraApolicesDocuments();
            Log::info('🇧🇷 Completado procesamiento BANDEIRA E APOLICES', ['vessel_id' => $this->record->id]);

            // Procesar documentos de SISTEMA DE GESTÃO
            Log::info('⚙️ Iniciando procesamiento SISTEMA DE GESTÃO', ['vessel_id' => $this->record->id]);
            $this->handleSistemaGestaoDocuments();
            Log::info('⚙️ Completado procesamiento SISTEMA DE GESTÃO', ['vessel_id' => $this->record->id]);

            // Procesar documentos exclusivos por tipo
            Log::info('🔐 Iniciando procesamiento DOCUMENTOS EXCLUSIVOS', ['vessel_id' => $this->record->id]);
            $this->handleExclusiveDocuments();
            Log::info('🔐 Completado procesamiento DOCUMENTOS EXCLUSIVOS', ['vessel_id' => $this->record->id]);

            $endTime = microtime(true);
            $processingTime = ($endTime - $startTime) * 1000; // en milisegundos

            Log::info('✅ ========== PROCESAMIENTO COMPLETO DE DOCUMENTOS FINALIZADO ==========', [
                'vessel_id' => $this->record->id,
                'processing_time_ms' => round($processingTime, 2),
                'end_memory' => memory_get_usage(true),
                'success' => true,
            ]);

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = ($endTime - $startTime) * 1000;

            Log::error('❌ ERROR EN PROCESAMIENTO DE DOCUMENTOS', [
                'vessel_id' => $this->record->id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'processing_time_ms' => round($processingTime, 2),
                'memory_at_error' => memory_get_usage(true),
            ]);
            throw $e;
        }
    }

    protected function handleBandeiraApolicesDocuments(): void
    {
        $startTime = microtime(true);
        $category = 'bandeira_apolices';
        $bandeiraDocuments = VesselDocumentType::getBandeiraApolicesDocuments();

        Log::info('🇧🇷 ===== PROCESANDO DOCUMENTOS BANDEIRA E APÓLICES =====', [
            'vessel_id' => $this->record->id,
            'vessel_name' => $this->record->name,
            'category' => $category,
            'available_document_types' => array_keys($bandeiraDocuments),
            'document_types_count' => count($bandeiraDocuments),
            'start_time' => now()->toDateTimeString(),
        ]);

        $processedFiles = 0;
        $skippedFields = 0;
        $errors = [];

        foreach ($bandeiraDocuments as $documentType => $documentName) {
            $fieldName = "document_{$documentType}";
            $files = $this->data[$fieldName] ?? [];
            $fileCount = is_array($files) ? count($files) : (empty($files) ? 0 : 1);

            Log::info("📋 PROCESANDO DOCUMENTO BANDEIRA: {$fieldName}", [
                'vessel_id' => $this->record->id,
                'document_type' => $documentType,
                'document_name' => $documentName,
                'field_name' => $fieldName,
                'files_count' => $fileCount,
                'has_files' => !empty($files),
                'files_type' => gettype($files),
                'memory_usage' => memory_get_usage(true),
            ]);

            if (!empty($files)) {
                if (is_array($files)) {
                    foreach ($files as $index => $file) {
                        if ($file) {
                            Log::info("📎 PROCESANDO ARCHIVO " . ($index + 1) . "/" . $fileCount . " de " . $fieldName, [
                                'vessel_id' => $this->record->id,
                                'file_index' => $index,
                                'file_type' => gettype($file),
                                'file_class' => is_object($file) ? get_class($file) : 'not_object',
                            ]);

                            try {
                                VesselResource::handleDocumentUpload(
                                    $file,
                                    $this->record,
                                    $documentType,
                                    $category,
                                    $documentName
                                );
                                $processedFiles++;

                                Log::info("✅ ARCHIVO PROCESADO EXITOSAMENTE", [
                                    'vessel_id' => $this->record->id,
                                    'field_name' => $fieldName,
                                    'file_index' => $index,
                                ]);

                            } catch (\Exception $e) {
                                $errors[] = [
                                    'field' => $fieldName,
                                    'file_index' => $index,
                                    'error' => $e->getMessage()
                                ];

                                Log::error("❌ ERROR PROCESANDO ARCHIVO " . $fieldName . "[" . $index . "]", [
                                    'vessel_id' => $this->record->id,
                                    'error_message' => $e->getMessage(),
                                    'error_trace' => $e->getTraceAsString(),
                                ]);
                            }
                        }
                    }
                } else {
                    // Archivo único
                    Log::info("📎 PROCESANDO ARCHIVO ÚNICO de {$fieldName}", [
                        'vessel_id' => $this->record->id,
                        'file_type' => gettype($files),
                    ]);

                    try {
                        VesselResource::handleDocumentUpload(
                            $files,
                            $this->record,
                            $documentType,
                            $category,
                            $documentName
                        );
                        $processedFiles++;
                    } catch (\Exception $e) {
                        $errors[] = [
                            'field' => $fieldName,
                            'error' => $e->getMessage()
                        ];
                    }
                }
            } else {
                $skippedFields++;
                Log::info("⏭️ CAMPO SIN ARCHIVOS: {$fieldName}", [
                    'vessel_id' => $this->record->id,
                    'reason' => 'no_files_provided'
                ]);
            }
        }

        $endTime = microtime(true);
        $processingTime = ($endTime - $startTime) * 1000;

        Log::info('🇧🇷 ===== BANDEIRA E APÓLICES COMPLETADO =====', [
            'vessel_id' => $this->record->id,
            'total_document_types' => count($bandeiraDocuments),
            'processed_files' => $processedFiles,
            'skipped_fields' => $skippedFields,
            'errors_count' => count($errors),
            'errors' => $errors,
            'processing_time_ms' => round($processingTime, 2),
            'success' => count($errors) === 0,
        ]);
    }

    protected function handleSistemaGestaoDocuments(): void
    {
        $startTime = microtime(true);
        $category = 'sistema_gestao';
        $sistemaDocuments = VesselDocumentType::getSistemaGestaoDocuments();

        Log::info('⚙️ ===== PROCESANDO DOCUMENTOS SISTEMA DE GESTÃO =====', [
            'vessel_id' => $this->record->id,
            'vessel_name' => $this->record->name,
            'category' => $category,
            'available_document_types' => array_keys($sistemaDocuments),
            'document_types_count' => count($sistemaDocuments),
            'start_time' => now()->toDateTimeString(),
        ]);

        $processedFiles = 0;
        $skippedFields = 0;
        $errors = [];

        foreach ($sistemaDocuments as $documentType => $documentName) {
            $fieldName = "document_{$documentType}";
            $files = $this->data[$fieldName] ?? [];
            $fileCount = is_array($files) ? count($files) : (empty($files) ? 0 : 1);

            Log::info("🔧 PROCESANDO DOCUMENTO SISTEMA: {$fieldName}", [
                'vessel_id' => $this->record->id,
                'document_type' => $documentType,
                'document_name' => $documentName,
                'field_name' => $fieldName,
                'files_count' => $fileCount,
                'has_files' => !empty($files),
                'files_type' => gettype($files),
            ]);

            if (!empty($files)) {
                if (is_array($files)) {
                    foreach ($files as $index => $file) {
                        if ($file) {
                            Log::info("📎 PROCESANDO ARCHIVO " . ($index + 1) . "/" . $fileCount . " de " . $fieldName, [
                                'vessel_id' => $this->record->id,
                                'file_index' => $index,
                            ]);

                            try {
                                VesselResource::handleDocumentUpload(
                                    $file,
                                    $this->record,
                                    $documentType,
                                    $category,
                                    $documentName
                                );
                                $processedFiles++;
                            } catch (\Exception $e) {
                                $errors[] = [
                                    'field' => $fieldName,
                                    'file_index' => $index,
                                    'error' => $e->getMessage()
                                ];
                            }
                        }
                    }
                } else {
                    try {
                        VesselResource::handleDocumentUpload(
                            $files,
                            $this->record,
                            $documentType,
                            $category,
                            $documentName
                        );
                        $processedFiles++;
                    } catch (\Exception $e) {
                        $errors[] = [
                            'field' => $fieldName,
                            'error' => $e->getMessage()
                        ];
                    }
                }
            } else {
                $skippedFields++;
                Log::info("⏭️ CAMPO SIN ARCHIVOS: {$fieldName}", ['vessel_id' => $this->record->id]);
            }
        }

        $endTime = microtime(true);
        $processingTime = ($endTime - $startTime) * 1000;

        Log::info('⚙️ ===== SISTEMA DE GESTÃO COMPLETADO =====', [
            'vessel_id' => $this->record->id,
            'total_document_types' => count($sistemaDocuments),
            'processed_files' => $processedFiles,
            'skipped_fields' => $skippedFields,
            'errors_count' => count($errors),
            'errors' => $errors,
            'processing_time_ms' => round($processingTime, 2),
            'success' => count($errors) === 0,
        ]);
    }

    protected function handleVesselAssociations(): void
    {
        $startTime = microtime(true);
        $associatedVessels = $this->data['associated_vessels'] ?? [];

        Log::info('🔗 ===== PROCESANDO ASOCIACIONES DE EMBARCACIONES =====', [
            'vessel_id' => $this->record->id,
            'vessel_name' => $this->record->name,
            'associated_vessels' => $associatedVessels,
            'associations_count' => count($associatedVessels),
            'has_associations' => !empty($associatedVessels),
            'start_time' => now()->toDateTimeString(),
        ]);

        $createdAssociations = 0;
        $errors = [];

        if (!empty($associatedVessels)) {
            foreach ($associatedVessels as $index => $associatedVesselId) {
                Log::info("🔗 CREANDO ASOCIACIÓN " . ($index + 1) . "/" . count($associatedVessels), [
                    'vessel_id' => $this->record->id,
                    'main_vessel_id' => $this->record->id,
                    'associated_vessel_id' => $associatedVesselId,
                    'association_index' => $index,
                ]);

                try {
                    $association = VesselAssociation::create([
                        'main_vessel_id' => $this->record->id,
                        'associated_vessel_id' => $associatedVesselId,
                    ]);

                    $createdAssociations++;

                    Log::info('✅ ASOCIACIÓN CREADA EXITOSAMENTE', [
                        'vessel_id' => $this->record->id,
                        'association_id' => $association->id,
                        'main_vessel_id' => $this->record->id,
                        'associated_vessel_id' => $associatedVesselId,
                        'created_at' => $association->created_at->toDateTimeString(),
                    ]);

                } catch (\Exception $e) {
                    $errors[] = [
                        'associated_vessel_id' => $associatedVesselId,
                        'error' => $e->getMessage()
                    ];

                    Log::error('❌ ERROR CREANDO ASOCIACIÓN', [
                        'vessel_id' => $this->record->id,
                        'main_vessel_id' => $this->record->id,
                        'associated_vessel_id' => $associatedVesselId,
                        'error_message' => $e->getMessage(),
                        'error_trace' => $e->getTraceAsString(),
                    ]);
                }
            }
        } else {
            Log::info('🚫 NO HAY ASOCIACIONES PARA PROCESAR', [
                'vessel_id' => $this->record->id,
                'reason' => 'no_associated_vessels_provided'
            ]);
        }

        $endTime = microtime(true);
        $processingTime = ($endTime - $startTime) * 1000;

        Log::info('🔗 ===== ASOCIACIONES COMPLETADAS =====', [
            'vessel_id' => $this->record->id,
            'total_associations_requested' => count($associatedVessels),
            'successfully_created' => $createdAssociations,
            'errors_count' => count($errors),
            'errors' => $errors,
            'processing_time_ms' => round($processingTime, 2),
            'success' => count($errors) === 0,
        ]);
    }

    protected function handleExclusiveDocuments(): void
    {
        $startTime = microtime(true);

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

        Log::info('🔐 ===== PROCESANDO DOCUMENTOS EXCLUSIVOS =====', [
            'vessel_id' => $this->record->id,
            'vessel_name' => $this->record->name,
            'vessel_type' => $this->record->vessel_type,
            'exclusive_document_types' => array_keys($documentMapping),
            'document_types_count' => count($documentMapping),
            'start_time' => now()->toDateTimeString(),
        ]);

        $processedFiles = 0;
        $skippedFields = 0;
        $errors = [];

        foreach ($documentMapping as $fieldName => $docInfo) {
            $files = $this->data[$fieldName] ?? [];
            $fileCount = is_array($files) ? count($files) : (empty($files) ? 0 : 1);

            Log::info("📄 PROCESANDO DOCUMENTO EXCLUSIVO: {$fieldName}", [
                'vessel_id' => $this->record->id,
                'document_type' => $docInfo['type'],
                'document_name' => $docInfo['name'],
                'category' => $docInfo['category'],
                'field_name' => $fieldName,
                'files_count' => $fileCount,
                'has_files' => !empty($files),
                'files_type' => gettype($files),
            ]);

            if (!empty($files)) {
                if (is_array($files)) {
                    foreach ($files as $index => $file) {
                        if ($file) {
                            Log::info("📎 PROCESANDO ARCHIVO EXCLUSIVO " . ($index + 1) . "/" . $fileCount, [
                                'vessel_id' => $this->record->id,
                                'field_name' => $fieldName,
                                'file_index' => $index,
                                'category' => $docInfo['category'],
                            ]);

                            try {
                                VesselResource::handleDocumentUpload(
                                    $file,
                                    $this->record,
                                    $docInfo['type'],
                                    $docInfo['category'],
                                    $docInfo['name']
                                );
                                $processedFiles++;

                                Log::info("✅ ARCHIVO EXCLUSIVO PROCESADO", [
                                    'vessel_id' => $this->record->id,
                                    'field_name' => $fieldName,
                                    'file_index' => $index,
                                ]);

                            } catch (\Exception $e) {
                                $errors[] = [
                                    'field' => $fieldName,
                                    'file_index' => $index,
                                    'category' => $docInfo['category'],
                                    'error' => $e->getMessage()
                                ];

                                Log::error("❌ ERROR EN DOCUMENTO EXCLUSIVO " . $fieldName . "[" . $index . "]", [
                                    'vessel_id' => $this->record->id,
                                    'error_message' => $e->getMessage(),
                                    'category' => $docInfo['category'],
                                ]);
                            }
                        }
                    }
                } else {
                    // Archivo único
                    try {
                        VesselResource::handleDocumentUpload(
                            $files,
                            $this->record,
                            $docInfo['type'],
                            $docInfo['category'],
                            $docInfo['name']
                        );
                        $processedFiles++;
                    } catch (\Exception $e) {
                        $errors[] = [
                            'field' => $fieldName,
                            'category' => $docInfo['category'],
                            'error' => $e->getMessage()
                        ];
                    }
                }
            } else {
                $skippedFields++;
                Log::info("⏭️ DOCUMENTO EXCLUSIVO SIN ARCHIVOS: {$fieldName}", [
                    'vessel_id' => $this->record->id,
                    'category' => $docInfo['category']
                ]);
            }
        }

        $endTime = microtime(true);
        $processingTime = ($endTime - $startTime) * 1000;

        Log::info('🔐 ===== DOCUMENTOS EXCLUSIVOS COMPLETADOS =====', [
            'vessel_id' => $this->record->id,
            'total_exclusive_types' => count($documentMapping),
            'processed_files' => $processedFiles,
            'skipped_fields' => $skippedFields,
            'errors_count' => count($errors),
            'errors' => $errors,
            'processing_time_ms' => round($processingTime, 2),
            'success' => count($errors) === 0,
        ]);
    }
}
