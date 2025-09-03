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
            $reporte = ReporteWord::with('checklistInspection.vessel')->findOrFail($id);
            
            // Verificar que el archivo existe
            $filePath = $reporte->report_path;
            $fullPath = storage_path('app/private/' . $filePath);
            
            // Depuración de rutas para identificar el problema
            if (!file_exists($fullPath)) {
                \Log::error('Archivo no encontrado: ' . $fullPath);
                \Log::info('ID del reporte: ' . $id);
                \Log::info('Ruta del archivo: ' . $filePath);
                
                // Verificar si el directorio existe
                $directory = dirname($fullPath);
                if (!file_exists($directory)) {
                    \Log::error('El directorio no existe: ' . $directory);
                }
                
                abort(404, 'El archivo no existe en la ruta: ' . $filePath);
            }
            
            // Obtener extensión del archivo
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            
            // Prepara el nombre del archivo para descarga
            $vesselName = $reporte->checklistInspection && $reporte->checklistInspection->vessel ? 
                          $reporte->checklistInspection->vessel->name : 'Checklist';
            $downloadName = 'Reporte_' . str_replace(' ', '_', $vesselName) . '_' . date('Y-m-d') . '.' . $extension;
            
            // Devolver el archivo directamente, evitando usar Storage
            return response()->download($fullPath, $downloadName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
                'Cache-Control' => 'max-age=0',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al descargar reporte: ' . $e->getMessage());
            abort(404, 'No se pudo encontrar el reporte solicitado. Error: ' . $e->getMessage());
        }
    }
}