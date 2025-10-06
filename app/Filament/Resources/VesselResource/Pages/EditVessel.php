<?php

namespace App\Filament\Resources\VesselResource\Pages;

use App\Filament\Resources\VesselResource;
use App\Models\VesselAssociation;
use App\Models\VesselDocument;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;

class EditVessel extends EditRecord
{
    protected static string $resource = VesselResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function beforeSave(): void
    {
        Log::info('💾 ========== BOTÓN GUARDAR PRESIONADO ==========', [
            'action' => 'EDIT_VESSEL',
            'vessel_id' => $this->record->id,
            'vessel_name' => $this->record->name,
            'user_id' => auth()->id(),
            'timestamp' => now()->toDateTimeString(),
            'form_data_count' => count($this->data),
        ]);

        // Log específico de campos de documentos para rastrear archivos PDF
        $documentFields = array_filter($this->data, function($key) {
            return str_starts_with($key, 'document_');
        }, ARRAY_FILTER_USE_KEY);

        Log::info('📄 ARCHIVOS PDF EN FORMULARIO AL GUARDAR', [
            'vessel_id' => $this->record->id,
            'document_fields_count' => count($documentFields),
            'document_details' => array_map(function($files, $key) {
                $details = [
                    'field' => $key,
                    'has_content' => !empty($files),
                    'type' => gettype($files),
                ];
                
                if (is_array($files)) {
                    $details['files_count'] = count($files);
                    $details['files_info'] = array_map(function($file, $index) {
                        if (is_object($file)) {
                            return [
                                'index' => $index,
                                'is_temporary' => true,
                                'class' => get_class($file),
                                'original_name' => method_exists($file, 'getClientOriginalName') ? $file->getClientOriginalName() : 'unknown',
                                'size' => method_exists($file, 'getSize') ? $file->getSize() : 'unknown',
                            ];
                        } else {
                            return [
                                'index' => $index,
                                'is_temporary' => false,
                                'is_string' => is_string($file),
                                'value' => is_string($file) ? basename($file) : 'unknown_type',
                            ];
                        }
                    }, $files, array_keys($files));
                } elseif (!empty($files)) {
                    $details['single_file'] = [
                        'is_object' => is_object($files),
                        'is_string' => is_string($files),
                        'value' => is_string($files) ? basename($files) : (is_object($files) ? get_class($files) : 'unknown'),
                    ];
                }
                
                return $details;
            }, $documentFields, array_keys($documentFields))
        ]);

        // Log todos los datos del formulario principales
        $mainFields = [
            'name', 'imo_number', 'vessel_type', 'flag_country', 'call_sign',
            'gross_tonnage', 'net_tonnage', 'length_overall', 'beam', 'depth',
            'built_year', 'shipyard_id', 'owner_id', 'port_of_registry',
            'service_type_id', 'navigation_type_id', 'associated_vessels'
        ];

        $mainData = [];
        $changedFields = [];
        foreach ($mainFields as $field) {
            if (isset($this->data[$field])) {
                $mainData[$field] = $this->data[$field];
                // Detectar cambios comparando con el record actual
                $originalValue = $this->record->{$field} ?? null;
                if ($originalValue != $this->data[$field]) {
                    $changedFields[$field] = [
                        'original' => $originalValue,
                        'new' => $this->data[$field]
                    ];
                }
            }
        }

        Log::info('🚢 VESSEL DATA - MAIN FIELDS EDIT', [
            'main_data' => $mainData,
            'changed_fields' => $changedFields,
            'changed_fields_count' => count($changedFields)
        ]);

        // Log todos los campos de documentos
        $documentFields = array_filter($this->data, function($key) {
            return str_starts_with($key, 'document_');
        }, ARRAY_FILTER_USE_KEY);

        Log::info('📄 VESSEL DATA - DOCUMENT FIELDS EDIT COMPLETE', [
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
                            'is_new_upload' => is_object($file) && !is_string($file),
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
                        'is_new_upload' => is_object($files) && !is_string($files),
                    ];
                }

                return [
                    'field' => $key,
                    'has_content' => !empty($files),
                    'files_count' => is_array($files) ? count($files) : (empty($files) ? 0 : 1),
                    'files_detail' => $fileDetails,
                    'has_new_uploads' => is_array($files) ?
                        array_reduce($files, function($carry, $file) { return $carry || (is_object($file) && !is_string($file)); }, false) :
                        (is_object($files) && !is_string($files))
                ];
            }, $documentFields, array_keys($documentFields))
        ]);

        // Log otros campos que no sean principales ni documentos
        $otherFields = array_diff_key($this->data, array_flip($mainFields), $documentFields);
        if (!empty($otherFields)) {
            Log::info('🔧 VESSEL DATA - OTHER FIELDS EDIT', [
                'other_fields' => $otherFields
            ]);
        }
    }

    protected function afterSave(): void
    {
        Log::info('✏️ ========== PROCESANDO DESPUÉS DE GUARDAR ==========', [
            'vessel_id' => $this->record->id,
            'vessel_name' => $this->record->name,
            'timestamp' => now()->toDateTimeString(),
        ]);

        // Verificar qué documentos existen actualmente en BD
        $existingDocuments = VesselDocument::where('vessel_id', $this->record->id)->get();
        
        Log::info('📄 DOCUMENTOS EXISTENTES EN BD ANTES DE PROCESAR', [
            'vessel_id' => $this->record->id,
            'existing_documents_count' => $existingDocuments->count(),
            'existing_documents' => $existingDocuments->map(function($doc) {
                return [
                    'id' => $doc->id,
                    'document_type' => $doc->document_type,
                    'file_name' => $doc->file_name,
                    'created_at' => $doc->created_at->toDateTimeString(),
                ];
            })->toArray()
        ]);

        // Log completo de todos los datos del formulario relevantes para edición
        $documentFields = array_filter($this->data, function($key) {
            return str_starts_with($key, 'document_');
        }, ARRAY_FILTER_USE_KEY);

        Log::info('📄 DOCUMENTOS A PROCESAR EN AFTERSAVE', [
            'vessel_id' => $this->record->id,
            'document_fields_count' => count($documentFields),
            'document_fields_with_content' => array_filter($documentFields, function($files) {
                return !empty($files);
            }),
        ]);

        Log::info('📄 FORM DATA EDIT - DOCUMENTOS Y ASOCIACIONES', [
            'vessel_id' => $this->record->id,
            'vessel_name' => $this->record->name,
            'document_fields_count' => count($documentFields),
            'document_fields_summary' => array_map(function($files, $key) {
                return [
                    'field' => $key,
                    'files_count' => is_array($files) ? count($files) : (empty($files) ? 0 : 1),
                    'has_content' => !empty($files),
                    'type' => gettype($files)
                ];
            }, $documentFields, array_keys($documentFields)),
            'associated_vessels' => $this->data['associated_vessels'] ?? [],
            'associated_vessels_count' => count($this->data['associated_vessels'] ?? []),
            'previous_updated_at' => $this->record->updated_at->toDateTimeString(),
        ]);

        try {
            $this->handleVesselAssociations();

            // Verificar documentos después de todo el procesamiento
            $finalDocuments = VesselDocument::where('vessel_id', $this->record->id)->get();
            
            Log::info('📋 DOCUMENTOS FINALES EN BD DESPUÉS DE GUARDAR', [
                'vessel_id' => $this->record->id,
                'final_documents_count' => $finalDocuments->count(),
                'new_documents_created' => $finalDocuments->where('created_at', '>=', now()->subMinute())->count(),
                'final_documents' => $finalDocuments->map(function($doc) {
                    return [
                        'id' => $doc->id,
                        'document_type' => $doc->document_type,
                        'file_name' => $doc->file_name,
                        'created_at' => $doc->created_at->toDateTimeString(),
                        'updated_at' => $doc->updated_at->toDateTimeString(),
                    ];
                })->toArray()
            ]);

            Log::info('✅ ========== EDITVESSEL AFTERSAVE COMPLETADO EXITOSAMENTE ==========', [
                'vessel_id' => $this->record->id,
                'vessel_name' => $this->record->name,
                'processing_time' => 'completed_at_' . now()->toDateTimeString(),
                'final_memory_usage' => memory_get_usage(true),
                'final_updated_at' => $this->record->fresh()->updated_at->toDateTimeString(),
            ]);

        } catch (\Exception $e) {
            Log::error('❌ ========== ERROR EN EDITVESSEL AFTERSAVE ==========', [
                'vessel_id' => $this->record->id,
                'vessel_name' => $this->record->name,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'form_data_keys' => array_keys($this->data),
            ]);
            throw $e;
        }
    }

    protected function handleVesselAssociations(): void
    {
        $startTime = microtime(true);
        $associatedVessels = $this->data['associated_vessels'] ?? [];

        Log::info('🔄 ===== EDITANDO ASOCIACIONES DE EMBARCACIONES =====', [
            'vessel_id' => $this->record->id,
            'vessel_name' => $this->record->name,
            'new_associated_vessels' => $associatedVessels,
            'new_associations_count' => count($associatedVessels),
            'has_new_associations' => !empty($associatedVessels),
            'start_time' => now()->toDateTimeString(),
        ]);

        // Obtener asociaciones actuales antes de eliminar
        $existingAssociations = VesselAssociation::where('main_vessel_id', $this->record->id)->get();

        Log::info('🗑️ ELIMINANDO ASOCIACIONES EXISTENTES', [
            'vessel_id' => $this->record->id,
            'existing_associations' => $existingAssociations->map(function($assoc) {
                return [
                    'id' => $assoc->id,
                    'associated_vessel_id' => $assoc->associated_vessel_id,
                    'created_at' => $assoc->created_at->toDateTimeString(),
                ];
            })->toArray(),
            'existing_count' => $existingAssociations->count(),
        ]);

        // Eliminar asociaciones existentes
        $deletedCount = VesselAssociation::where('main_vessel_id', $this->record->id)->delete();

        Log::info('🗑️ ASOCIACIONES ELIMINADAS', [
            'vessel_id' => $this->record->id,
            'deleted_count' => $deletedCount,
            'expected_count' => $existingAssociations->count(),
            'deletion_successful' => $deletedCount === $existingAssociations->count(),
        ]);

        $createdAssociations = 0;
        $errors = [];

        // Crear nuevas asociaciones
        if (!empty($associatedVessels)) {
            Log::info('➕ CREANDO NUEVAS ASOCIACIONES', [
                'vessel_id' => $this->record->id,
                'new_associations_to_create' => count($associatedVessels),
            ]);

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

                    Log::info('✅ NUEVA ASOCIACIÓN CREADA', [
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

                    Log::error('❌ ERROR CREANDO NUEVA ASOCIACIÓN', [
                        'vessel_id' => $this->record->id,
                        'main_vessel_id' => $this->record->id,
                        'associated_vessel_id' => $associatedVesselId,
                        'error_message' => $e->getMessage(),
                        'error_trace' => $e->getTraceAsString(),
                    ]);
                }
            }
        } else {
            Log::info('🚫 NO HAY NUEVAS ASOCIACIONES PARA CREAR', [
                'vessel_id' => $this->record->id,
                'reason' => 'no_associated_vessels_provided'
            ]);
        }

        $endTime = microtime(true);
        $processingTime = ($endTime - $startTime) * 1000;

        Log::info('🔄 ===== EDICIÓN DE ASOCIACIONES COMPLETADA =====', [
            'vessel_id' => $this->record->id,
            'previous_associations_count' => $existingAssociations->count(),
            'deleted_associations' => $deletedCount,
            'new_associations_requested' => count($associatedVessels),
            'successfully_created' => $createdAssociations,
            'errors_count' => count($errors),
            'errors' => $errors,
            'processing_time_ms' => round($processingTime, 2),
            'success' => count($errors) === 0 && $createdAssociations === count($associatedVessels),
            'final_associations_count' => VesselAssociation::where('main_vessel_id', $this->record->id)->count(),
        ]);
    }
}
