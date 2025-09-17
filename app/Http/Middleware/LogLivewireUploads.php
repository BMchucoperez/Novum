<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogLivewireUploads
{
    public function handle(Request $request, Closure $next)
    {
        // Solo loggear requests de upload de Livewire
        if ($request->is('livewire/upload-file*')) {
            Log::info('Livewire Upload Request', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'headers' => $request->headers->all(),
                'files' => $request->allFiles(),
                'file_count' => count($request->allFiles()),
                'content_length' => $request->header('Content-Length'),
                'content_type' => $request->header('Content-Type'),
            ]);

            // Validar archivos específicamente
            foreach ($request->allFiles() as $key => $files) {
                // $files puede ser un array de archivos o un solo archivo
                $fileArray = is_array($files) ? $files : [$files];
                
                foreach ($fileArray as $index => $file) {
                    if ($file && method_exists($file, 'getClientOriginalName')) {
                        try {
                            Log::info('File Details', [
                                'key' => $key,
                                'index' => $index,
                                'original_name' => $file->getClientOriginalName(),
                                'size' => $file->getSize(),
                                'path' => $file->getPathname(),
                                'real_path' => $file->getRealPath(),
                                'extension' => $file->getClientOriginalExtension(),
                                'is_valid' => $file->isValid(),
                                'error' => $file->getError(),
                                'error_message' => $file->getErrorMessage(),
                            ]);
                            
                            // Solo intentar getMimeType si el archivo existe
                            if ($file->isValid() && $file->getPathname()) {
                                Log::info('File MIME Type', [
                                    'key' => $key,
                                    'mime_type' => $file->getMimeType(),
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::error('Error processing file details', [
                                'key' => $key,
                                'index' => $index,
                                'error' => $e->getMessage(),
                                'file_class' => get_class($file),
                            ]);
                        }
                    } else {
                        Log::warning('Invalid file object', [
                            'key' => $key,
                            'index' => $index,
                            'file_type' => gettype($file),
                            'file_class' => is_object($file) ? get_class($file) : 'not_object',
                        ]);
                    }
                }
            }
        }

        $response = $next($request);

        // Loggear la respuesta si es un upload de Livewire
        if ($request->is('livewire/upload-file*')) {
            Log::info('Livewire Upload Response', [
                'status' => $response->getStatusCode(),
                'content' => $response->getContent(),
            ]);
        }

        return $response;
    }
}