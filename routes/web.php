<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;

Route::get('/', function () {
    return redirect('/admin');
});

// Ruta temporal para verificar configuración de uploads
Route::get('/upload-test', function () {
    return response()->json([
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'max_execution_time' => ini_get('max_execution_time'),
        'memory_limit' => ini_get('memory_limit'),
        'max_file_uploads' => ini_get('max_file_uploads'),
        'max_input_time' => ini_get('max_input_time'),
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
        'php_sapi_name' => php_sapi_name(),
    ]);
});

// Route for downloading vessel documents
Route::get('/documents/{id}/download', [DocumentController::class, 'download'])->name('documents.download');

// Route for downloading reporte words
Route::get('/reporte-word/download/{id}', [DocumentController::class, 'downloadReporteWord'])->name('reporte-word.download');

// Route for downloading reporte PDF
Route::get('/reporte-word/download-pdf/{id}', [DocumentController::class, 'downloadReportePDF'])->name('reporte-word.download-pdf');

