<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VesselDocument;
use App\Models\ReporteWord;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function download($id)
    {
        $document = VesselDocument::findOrFail($id);
        
        // Verificar si el archivo existe
        if (!file_exists(storage_path('app/private/' . $document->file_path))) {
            abort(404);
        }
        
        return response()->download(storage_path('app/private/' . $document->file_path))->deleteFileAfterSend(false);
    }
    
    public function downloadReporteWord($id)
    {
        try {
            // Encontrar el reporte
            $reporte = ReporteWord::with(['checklistInspection.vessel', 'checklistInspection.owner'])->findOrFail($id);
            
            // Verificar que el archivo existe
            $filePath = $reporte->report_path;
            
            if (empty($filePath)) {
                \Log::error('Ruta de archivo vacía para reporte ID: ' . $id);
                abort(404, 'La ruta del archivo no está definida.');
            }
            
            $fullPath = storage_path('app/private/' . $filePath);
            
            // Depuración detallada
            \Log::info('Intentando descargar reporte:', [
                'reporte_id' => $id,
                'file_path' => $filePath,
                'full_path' => $fullPath,
                'file_exists' => file_exists($fullPath)
            ]);
            
            if (!file_exists($fullPath)) {
                \Log::error('Archivo no encontrado:', [
                    'reporte_id' => $id,
                    'expected_path' => $fullPath,
                    'file_path_in_db' => $filePath
                ]);
                
                // Verificar si el directorio existe
                $directory = dirname($fullPath);
                if (!file_exists($directory)) {
                    \Log::error('El directorio no existe: ' . $directory);
                }
                
                // Intentar encontrar archivos similares en el directorio
                $reportsDir = storage_path('app/private/reports');
                if (file_exists($reportsDir)) {
                    $availableFiles = scandir($reportsDir);
                    \Log::info('Archivos disponibles en reports:', $availableFiles);
                }
                
                abort(404, 'El archivo del reporte no existe. Es posible que haya sido eliminado o que la ruta esté corrupta.');
            }
            
            // Verificar que el archivo no esté vacío o corrupto
            $fileSize = filesize($fullPath);
            if ($fileSize === false || $fileSize < 1000) {
                \Log::error('Archivo corrupto o muy pequeño:', [
                    'file_path' => $fullPath,
                    'file_size' => $fileSize
                ]);
                abort(404, 'El archivo del reporte está corrupto o es demasiado pequeño.');
            }
            
            // Obtener extensión del archivo
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            
            // Preparar el nombre del archivo para descarga
            $vesselName = 'Sin_Embarcacion';
            if ($reporte->checklistInspection && $reporte->checklistInspection->vessel) {
                $vesselName = $reporte->checklistInspection->vessel->name;
            }
            
            $ownerName = 'Sin_Propietario';
            if ($reporte->checklistInspection && $reporte->checklistInspection->owner) {
                $ownerName = $reporte->checklistInspection->owner->name;
            }
            
            // Limpiar caracteres especiales para el nombre del archivo
            $cleanVesselName = preg_replace('/[^A-Za-z0-9_-]/', '_', $vesselName);
            $cleanOwnerName = preg_replace('/[^A-Za-z0-9_-]/', '_', $ownerName);
            
            $downloadName = 'Reporte_' . $cleanOwnerName . '_' . $cleanVesselName . '_' . date('Y-m-d_H-i-s') . '.' . $extension;
            
            \Log::info('Descargando archivo:', [
                'original_path' => $fullPath,
                'download_name' => $downloadName,
                'file_size' => $fileSize
            ]);
            
            // Determinar el Content-Type basado en la extensión
            $contentType = match(strtolower($extension)) {
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'doc' => 'application/msword',
                'pdf' => 'application/pdf',
                default => 'application/octet-stream'
            };
            
            // Devolver el archivo para descarga
            return response()->download($fullPath, $downloadName, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
                'Cache-Control' => 'no-cache, must-revalidate',
                'Expires' => '0',
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Reporte no encontrado: ID ' . $id);
            abort(404, 'El reporte solicitado no existe en la base de datos.');
        } catch (\Exception $e) {
            \Log::error('Error inesperado al descargar reporte:', [
                'reporte_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Ocurrió un error interno al intentar descargar el reporte: ' . $e->getMessage());
        }
    }
}