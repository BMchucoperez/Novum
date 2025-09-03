<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;

Route::get('/', function () {
    return view('welcome');
});

// Ruta para descargar documentos de embarcaciones
Route::get('/documents/download/{id}', [DocumentController::class, 'download'])->name('documents.download');

// Ruta para descargar informes Word
Route::get('/reporte-word/download/{id}', [DocumentController::class, 'downloadReporteWord'])->name('reporte-word.download');