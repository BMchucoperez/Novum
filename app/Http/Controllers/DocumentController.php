<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VesselDocument;
use App\Models\ReporteWord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    public function download($id)
    {
        $document = VesselDocument::findOrFail($id);

        // Verificar si el archivo existe en el storage público
        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404);
        }

        // Descargar el archivo usando el Storage facade
        return response()->download(Storage::disk('public')->path($document->file_path), $document->file_name);
    }
    
    public function downloadReporteWord($id)
    {
        try {
            $reporte = ReporteWord::findOrFail($id);
            
            // Verificar que el archivo existe
            $fullPath = storage_path('app/private/' . $reporte->report_path);
            
            if (!file_exists($fullPath)) {
                abort(404, 'El archivo del reporte no existe.');
            }
            
            // Verificar que no esté corrupto
            if (filesize($fullPath) < 1000) {
                abort(404, 'El archivo del reporte está corrupto.');
            }
            
            // Usar el nombre original del archivo
            $downloadName = basename($reporte->report_path);
            
            return response()->download($fullPath, $downloadName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Cache-Control' => 'no-store',
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'El reporte solicitado no existe.');
        } catch (\Exception $e) {
            Log::error('Error descargando reporte Word:', [
                'reporte_id' => $id,
                'error' => $e->getMessage()
            ]);
            abort(500, 'Error al descargar: ' . $e->getMessage());
        }
    }

    public function downloadReportePDF($id)
    {
        try {
            $reporte = ReporteWord::findOrFail($id);

            // Verificar que el PDF existe
            if (empty($reporte->pdf_path)) {
                abort(404, 'El reporte PDF no está disponible.');
            }

            $fullPath = storage_path('app/private/' . $reporte->pdf_path);

            if (!file_exists($fullPath)) {
                abort(404, 'El archivo PDF del reporte no existe.');
            }

            // Verificar que no esté corrupto
            if (filesize($fullPath) < 1000) {
                abort(404, 'El archivo PDF del reporte está corrupto.');
            }

            // Usar el nombre original del archivo
            $downloadName = basename($reporte->pdf_path);

            return response()->download($fullPath, $downloadName, [
                'Content-Type' => 'application/pdf',
                'Cache-Control' => 'no-store',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'El reporte solicitado no existe.');
        } catch (\Exception $e) {
            Log::error('Error descargando reporte PDF:', [
                'reporte_id' => $id,
                'error' => $e->getMessage()
            ]);
            abort(500, 'Error al descargar: ' . $e->getMessage());
        }
    }

}