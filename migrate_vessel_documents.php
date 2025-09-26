<?php

/**
 * Script de migración para mover documentos de embarcaciones
 * De: storage/app/private/public/vessel-documents (ubicación antigua)
 * A: storage/app/public/vessel-documents (ubicación nueva)
 *
 * También actualiza los registros en la base de datos para que apunten a las nuevas rutas.
 *
 * Ejecutar con: php migrate_vessel_documents.php
 */

require_once 'vendor/autoload.php';

// Cargar la aplicación Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\VesselDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

echo "🚢 ===== MIGRACIÓN DE DOCUMENTOS DE EMBARCACIONES ===== \n\n";

$startTime = time();
$totalMigrated = 0;
$totalErrors = 0;
$totalSkipped = 0;

// Directorios
$oldBaseDir = storage_path('app/private/public/vessel-documents');
$newBaseDir = storage_path('app/public/vessel-documents');

echo "📂 DIRECTORIOS DE MIGRACIÓN:\n";
echo "- Origen: {$oldBaseDir}\n";
echo "- Destino: {$newBaseDir}\n\n";

// Verificar si existe el directorio origen
if (!is_dir($oldBaseDir)) {
    echo "ℹ️ No se encontró el directorio origen. Es posible que no haya archivos que migrar.\n";
    echo "   Directorio buscado: {$oldBaseDir}\n\n";
    exit(0);
}

// Crear directorio destino si no existe
if (!is_dir($newBaseDir)) {
    echo "📁 Creando directorio destino...\n";
    File::makeDirectory($newBaseDir, 0755, true);
}

// Función para migrar recursivamente
function migrateDirectory($sourceDir, $targetDir, &$totalMigrated, &$totalErrors, &$totalSkipped) {
    if (!is_dir($sourceDir)) {
        return;
    }

    $items = scandir($sourceDir);

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $sourcePath = $sourceDir . '/' . $item;
        $targetPath = $targetDir . '/' . $item;

        if (is_dir($sourcePath)) {
            // Es un directorio - crear si no existe y migrar recursivamente
            if (!is_dir($targetPath)) {
                File::makeDirectory($targetPath, 0755, true);
                echo "📁 Directorio creado: " . basename($targetPath) . "\n";
            }

            migrateDirectory($sourcePath, $targetPath, $totalMigrated, $totalErrors, $totalSkipped);
        } else {
            // Es un archivo - migrar
            try {
                if (file_exists($targetPath)) {
                    echo "⚠️  Archivo ya existe en destino: {$item} - OMITIDO\n";
                    $totalSkipped++;
                    continue;
                }

                if (copy($sourcePath, $targetPath)) {
                    echo "✅ Migrado: {$item}\n";
                    $totalMigrated++;

                    // Opcional: eliminar archivo original después de copia exitosa
                    // unlink($sourcePath);
                } else {
                    echo "❌ Error copiando: {$item}\n";
                    $totalErrors++;
                }
            } catch (Exception $e) {
                echo "❌ Error con archivo {$item}: " . $e->getMessage() . "\n";
                $totalErrors++;
            }
        }
    }
}

echo "🔄 INICIANDO MIGRACIÓN DE ARCHIVOS...\n\n";

// Migrar archivos
migrateDirectory($oldBaseDir, $newBaseDir, $totalMigrated, $totalErrors, $totalSkipped);

echo "\n📊 RESULTADO DE MIGRACIÓN DE ARCHIVOS:\n";
echo "- Archivos migrados: {$totalMigrated}\n";
echo "- Errores: {$totalErrors}\n";
echo "- Omitidos (ya existían): {$totalSkipped}\n\n";

// Actualizar base de datos
echo "🗄️ ACTUALIZANDO REGISTROS EN BASE DE DATOS...\n\n";

$dbUpdated = 0;
$dbErrors = 0;

try {
    // Buscar todos los documentos con rutas que empiecen con "public/"
    $documentsToUpdate = VesselDocument::where('file_path', 'LIKE', 'public/vessel-documents/%')->get();

    echo "📋 Encontrados {$documentsToUpdate->count()} registros en BD que necesitan actualización\n\n";

    foreach ($documentsToUpdate as $document) {
        $oldPath = $document->file_path;
        $newPath = str_replace('public/vessel-documents/', 'vessel-documents/', $oldPath);

        // Verificar que el archivo existe en la nueva ubicación
        $newFilePath = storage_path('app/public/' . $newPath);

        if (file_exists($newFilePath)) {
            $document->file_path = $newPath;
            $document->save();

            echo "✅ BD actualizada - ID:{$document->id} | {$oldPath} → {$newPath}\n";
            $dbUpdated++;
        } else {
            echo "⚠️  Archivo no encontrado en nueva ubicación - ID:{$document->id} | Ruta: {$newPath}\n";
            $dbErrors++;
        }
    }

} catch (Exception $e) {
    echo "❌ Error actualizando BD: " . $e->getMessage() . "\n";
    $dbErrors++;
}

echo "\n📊 RESULTADO DE ACTUALIZACIÓN DE BD:\n";
echo "- Registros actualizados: {$dbUpdated}\n";
echo "- Errores: {$dbErrors}\n\n";

// Verificación final
echo "🔍 VERIFICACIÓN FINAL...\n\n";

$remainingOldFiles = 0;
if (is_dir($oldBaseDir)) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($oldBaseDir));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $remainingOldFiles++;
        }
    }
}

$newFiles = 0;
if (is_dir($newBaseDir)) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($newBaseDir));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $newFiles++;
        }
    }
}

echo "📂 Archivos restantes en ubicación antigua: {$remainingOldFiles}\n";
echo "📂 Archivos en nueva ubicación: {$newFiles}\n";

// Resumen final
$endTime = time();
$duration = $endTime - $startTime;

echo "\n🎯 ===== MIGRACIÓN COMPLETADA ===== \n";
echo "⏱️  Tiempo total: {$duration} segundos\n";
echo "📁 Archivos migrados: {$totalMigrated}\n";
echo "🗄️  Registros BD actualizados: {$dbUpdated}\n";
echo "❌ Errores totales: " . ($totalErrors + $dbErrors) . "\n\n";

if ($totalMigrated > 0) {
    echo "✅ MIGRACIÓN EXITOSA!\n";
    echo "📝 PRÓXIMOS PASOS:\n";
    echo "   1. Verificar que las descargas funcionen en el panel admin\n";
    echo "   2. Ejecutar: php artisan storage:link (si no está hecho)\n";
    echo "   3. Si todo funciona, eliminar directorio antiguo: {$oldBaseDir}\n\n";
} else {
    echo "ℹ️ No se encontraron archivos para migrar.\n";
    echo "   Es posible que ya estén en la ubicación correcta.\n\n";
}

echo "🔗 Recuerda verificar el enlace simbólico:\n";
echo "   ls -la public/storage\n";
echo "   Debe apuntar a: ../storage/app/public\n\n";