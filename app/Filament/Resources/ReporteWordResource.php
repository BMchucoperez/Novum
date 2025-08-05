<?php

namespace App\Filament\Resources;

use App\Models\ReporteWord;
use App\Models\StructureAndMachinery;
use App\Models\StatutoryCertificate;
use App\Models\OnboardManagementDocument;
use App\Models\CrewMember;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class ReporteWordResource extends Resource
{
    private static function cleanText($text): string
    {
        if (empty($text)) {
            return '';
        }
        
        // Convertir a string y manejar valores null/array
        if (is_array($text)) {
            $text = implode(' ', $text);
        }
        $text = (string) $text;
        
        // Si el texto está vacío después de la conversión, retornar cadena vacía
        if (trim($text) === '') {
            return '';
        }
        
        // Convertir a UTF-8 si no lo está, con manejo de errores
        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'auto');
            if ($text === false) {
                return 'Texto no válido';
            }
        }
        
        // Remover caracteres de control y no imprimibles más agresivamente
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/', '', $text);
        
        // Limpiar caracteres especiales problemáticos para Word de forma más completa
        $replacements = [
            // Comillas curvas
            '/[\x{201C}\x{201D}\x{201E}\x{201F}]/u' => '"',
            '/[\x{2018}\x{2019}\x{201A}\x{201B}]/u' => "'",
            
            // Guiones y rayas
            '/[\x{2013}\x{2014}\x{2015}]/u' => '-',
            
            // Espacios especiales
            '/[\x{00A0}\x{2000}-\x{200B}\x{2028}\x{2029}]/u' => ' ',
            
            // Puntos suspensivos
            '/[\x{2026}]/u' => '...',
            
            // Otros caracteres problemáticos
            '/[\x{00AD}]/u' => '', // soft hyphen
            '/[\x{FEFF}]/u' => '', // BOM
            '/[\x{200C}\x{200D}]/u' => '', // zero width characters
        ];
        
        foreach ($replacements as $pattern => $replacement) {
            $text = preg_replace($pattern, $replacement, $text);
        }
        
        // Normalizar espacios en blanco y saltos de línea
        $text = preg_replace('/[\r\n]+/', ' ', $text); // Convertir saltos de línea a espacios
        $text = preg_replace('/\s+/', ' ', $text); // Múltiples espacios a uno solo
        
        // Limpiar caracteres que pueden causar problemas en XML/Word
        $text = str_replace(['<', '>', '&'], ['&lt;', '&gt;', '&amp;'], $text);
        
        $text = trim($text);
        
        // Limitar longitud para evitar problemas de memoria y rendimiento
        if (mb_strlen($text, 'UTF-8') > 1000) {
            $text = mb_substr($text, 0, 997, 'UTF-8') . '...';
        }
        
        // Verificación final: asegurar que el texto es válido
        if (!mb_check_encoding($text, 'UTF-8')) {
            return 'Texto procesado no válido';
        }
        
        return $text;
    }





    private static function addSimpleModuleData($section, $titulo, $record): void
    {
        try {
            $normalStyle = ['size' => 11, 'name' => 'Arial'];
            $boldStyle = ['size' => 11, 'bold' => true, 'name' => 'Arial'];
            
            switch ($titulo) {
                case 'Estructura y Maquinaria':
                    for ($i = 1; $i <= 13; $i++) {
                        $items = $record->{"parte_{$i}_items"} ?? [];
                        if (!empty($items) && is_array($items)) {
                            $section->addText("Parte $i:", $boldStyle);
                            
                            foreach ($items as $item) {
                                if (is_array($item) && isset($item['item'])) {
                                    $itemText = self::cleanText($item['item']);
                                    $estado = isset($item['estado']) ? self::cleanText($item['estado']) : '';
                                    
                                    if (!empty($itemText)) {
                                        $line = "- $itemText";
                                        if (!empty($estado)) {
                                            $line .= " (Estado: $estado)";
                                        }
                                        $section->addText($line, $normalStyle);
                                    }
                                }
                            }
                            $section->addTextBreak(1);
                        }
                    }
                    break;
                    
                case 'Certificados Estatutarios':
                    for ($i = 1; $i <= 6; $i++) {
                        $items = $record->{"parte_{$i}_items"} ?? [];
                        if (!empty($items) && is_array($items)) {
                            $section->addText("Parte $i:", $boldStyle);
                            
                            foreach ($items as $item) {
                                if (is_array($item) && isset($item['item'])) {
                                    $itemText = self::cleanText($item['item']);
                                    $estado = isset($item['estado']) ? self::cleanText($item['estado']) : '';
                                    
                                    if (!empty($itemText)) {
                                        $line = "- $itemText";
                                        if (!empty($estado)) {
                                            $line .= " (Estado: $estado)";
                                        }
                                        $section->addText($line, $normalStyle);
                                    }
                                }
                            }
                            $section->addTextBreak(1);
                        }
                    }
                    break;
                    
                case 'Documentos de Gestion a Bordo':
                    for ($i = 1; $i <= 3; $i++) {
                        $items = $record->{"parte_{$i}_items"} ?? [];
                        if (!empty($items) && is_array($items)) {
                            $section->addText("Parte $i:", $boldStyle);
                            
                            foreach ($items as $item) {
                                if (is_array($item) && isset($item['item'])) {
                                    $itemText = self::cleanText($item['item']);
                                    $estado = isset($item['estado']) ? self::cleanText($item['estado']) : '';
                                    
                                    if (!empty($itemText)) {
                                        $line = "- $itemText";
                                        if (!empty($estado)) {
                                            $line .= " (Estado: $estado)";
                                        }
                                        $section->addText($line, $normalStyle);
                                    }
                                }
                            }
                            $section->addTextBreak(1);
                        }
                    }
                    break;
                    
                case 'Tripulacion':
                    $tripulantes = $record->tripulantes ?? [];
                    if (!empty($tripulantes) && is_array($tripulantes)) {
                        $section->addText("Lista de Tripulacion:", $boldStyle);
                        
                        foreach ($tripulantes as $t) {
                            if (is_array($t)) {
                                $cargo = isset($t['cargo']) ? self::cleanText($t['cargo']) : '';
                                $nombre = isset($t['nombre']) ? self::cleanText($t['nombre']) : '';
                                $matricula = isset($t['matricula']) ? self::cleanText($t['matricula']) : '';
                                
                                if (!empty($nombre)) {
                                    $line = "- $nombre";
                                    if (!empty($cargo)) {
                                        $line .= " ($cargo)";
                                    }
                                    if (!empty($matricula)) {
                                        $line .= " - Matricula: $matricula";
                                    }
                                    $section->addText($line, $normalStyle);
                                }
                            }
                        }
                    } else {
                        $section->addText('No se registraron tripulantes.', $normalStyle);
                    }
                    break;
            }
            
            // Agregar observaciones generales si existen
            if (isset($record->general_observations) && !empty($record->general_observations)) {
                $section->addText('Observaciones Generales:', $boldStyle);
                $cleanObservations = self::cleanText($record->general_observations);
                if (!empty($cleanObservations)) {
                    $section->addText($cleanObservations, $normalStyle);
                }
                $section->addTextBreak(1);
            }
            
        } catch (\Throwable $e) {
            $section->addText("Error procesando datos del modulo", ['size' => 11, 'name' => 'Arial']);
            \Log::error("Error en addSimpleModuleData: " . $e->getMessage());
        }
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->whereNotNull('owner_id')
            ->whereNotNull('vessel_id')
            ->whereNotNull('inspector_name')
            ->whereNotNull('inspection_date');
    }
    protected static ?string $model = ReporteWord::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';
    protected static ?string $navigationGroup = 'Exportaciones';
    protected static ?string $navigationLabel = 'Informes Generados';
    protected static ?string $pluralModelLabel = 'Informes Generados';

        public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('inspection_date')->label('Fecha')->date('d/m/Y'),
                TextColumn::make('owner.name')->label('Propietario'),
                TextColumn::make('vessel.name')->label('Embarcación'),
                TextColumn::make('inspector_name')->label('Inspector'),
            ])
            ->actions([
                Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->tooltip('Descargar')
                    ->action(function ($record) {
                        try {
                            // Verificar si el archivo existe
                            if (!$record->file_path) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Error de descarga')
                                    ->body('No se encontró la ruta del archivo')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            if (!Storage::disk('public')->exists($record->file_path)) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Archivo no encontrado')
                                    ->body('El archivo ya no existe en el servidor')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            // Verificar que el archivo no esté vacío
                            if (Storage::disk('public')->size($record->file_path) < 100) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Archivo corrupto')
                                    ->body('El archivo parece estar corrupto o vacío')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            return Storage::disk('public')->download($record->file_path);
                            
                        } catch (\Throwable $e) {
                            \Log::error('Error descargando archivo: ' . $e->getMessage());
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Error de descarga')
                                ->body('No se pudo descargar el archivo: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->headerActions([
                Action::make('generateReport')
                    ->label('Generar Informe')
                    ->icon('heroicon-o-document-text')
                    ->form([
                        Select::make('owner_id')->label('Propietario')->relationship('owner', 'name')->required(),
                        Select::make('vessel_id')->label('Embarcación 1')->relationship('vessel', 'name')->required(),
                        Select::make('vessel2_id')->label('Embarcación 2')->relationship('vessel2', 'name'),
                        Select::make('vessel3_id')->label('Embarcación 3')->relationship('vessel3', 'name'),
                        DatePicker::make('inspection_date')->label('Fecha de Inspección')->required(),
                        TextInput::make('inspector_name')->label('Nombre del Inspector')->required(),
                    ])
                    ->action(function (array $data) {
                        try {
                            // Configurar límites de memoria y tiempo de forma más agresiva
                            ini_set('memory_limit', '2048M');
                            ini_set('max_execution_time', 0); // Sin límite de tiempo
                            set_time_limit(0); // Sin límite de tiempo
                            
                            // Limpiar todos los buffers de salida
                            while (ob_get_level()) {
                                ob_end_clean();
                            }
                            
                            $fileName = 'informe_consolidado_' . now()->format('Ymd_His') . '_' . uniqid() . '.docx';
                            $directory = storage_path('app/public/reports');
                            if (!file_exists($directory)) {
                                mkdir($directory, 0755, true);
                            }
                            $filePath = 'reports/' . $fileName;
                            $fullPath = storage_path('app/public/' . $filePath);

                            // Crear documento Word básico
                            $phpWord = new \PhpOffice\PhpWord\PhpWord();
                            
                            // Crear sección simple
                            $section = $phpWord->addSection();
                            
                            // Estilos básicos
                            $titleStyle = ['size' => 16, 'bold' => true];
                            $headerStyle = ['size' => 14, 'bold' => true];
                            $normalStyle = ['size' => 11];
                            
                            // Título principal
                            $section->addText('INFORME CONSOLIDADO DE INSPECCION', $titleStyle);
                            $section->addTextBreak(2);
                            
                            // Información básica
                            $section->addText('INFORMACION GENERAL', $headerStyle);
                            $section->addTextBreak(1);
                            
                            // Datos básicos
                            $inspectionDate = \Carbon\Carbon::parse($data['inspection_date'])->format('d/m/Y');
                            $inspectorName = $data['inspector_name'] ?? 'No especificado';
                            
                            $section->addText("Fecha de Inspeccion: $inspectionDate", $normalStyle);
                            $section->addText("Inspector: $inspectorName", $normalStyle);
                            
                            // Obtener información de propietario y embarcación
                            try {
                                $owner = \App\Models\Owner::find($data['owner_id']);
                                $ownerName = $owner ? $owner->name : 'No disponible';
                                $section->addText("Propietario: $ownerName", $normalStyle);
                            } catch (\Exception $e) {
                                $section->addText('Propietario: No disponible', $normalStyle);
                            }
                            
                            try {
                                $vessel = \App\Models\Vessel::find($data['vessel_id']);
                                $vesselName = $vessel ? $vessel->name : 'No disponible';
                                $section->addText("Embarcacion: $vesselName", $normalStyle);
                            } catch (\Exception $e) {
                                $section->addText('Embarcacion: No disponible', $normalStyle);
                            }
                            
                            $section->addTextBreak(2);

                            // Agregar contenido básico del informe
                            $section->addText('CONTENIDO DEL INFORME', $headerStyle);
                            $section->addTextBreak(1);
                            
                            // Procesar módulos de forma muy simple
                            $models = [
                                'Estructura y Maquinaria' => \App\Models\StructureAndMachinery::class,
                                'Certificados Estatutarios' => \App\Models\StatutoryCertificate::class,
                                'Documentos de Gestion a Bordo' => \App\Models\OnboardManagementDocument::class,
                                'Tripulacion' => \App\Models\CrewMember::class,
                            ];

                            $found = false;

                            foreach ($models as $titulo => $modelClass) {
                                try {
                                    $count = $modelClass::where('owner_id', $data['owner_id'])
                                        ->where('vessel_id', $data['vessel_id'])
                                        ->whereDate('inspection_date', $data['inspection_date'])
                                        ->count();
                                    
                                    if ($count > 0) {
                                        $found = true;
                                        $section->addText("$titulo: $count registros encontrados", $normalStyle);
                                    }
                                    
                                } catch (\Exception $e) {
                                    $section->addText("$titulo: Error al procesar", $normalStyle);
                                }
                            }

                            if (!$found) {
                                $section->addText('No se encontraron registros para los criterios especificados.', $normalStyle);
                            }
                            
                            $section->addTextBreak(2);
                            $section->addText('Informe generado el: ' . now()->format('d/m/Y H:i:s'), $normalStyle);

                            // Guardar el documento con configuración más simple y robusta
                            try {
                                // Crear writer básico
                                $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                                
                                // Guardar el archivo directamente
                                $writer->save($fullPath);
                                
                                // Verificación básica de integridad
                                if (!file_exists($fullPath)) {
                                    throw new \Exception('El archivo no se creó correctamente');
                                }
                                
                                $fileSize = filesize($fullPath);
                                if ($fileSize < 1000) { // Archivo muy pequeño
                                    throw new \Exception("El archivo generado es muy pequeño ($fileSize bytes)");
                                }
                                
                                // Liberar memoria
                                unset($writer);
                                
                            } catch (\Exception $e) {
                                // Si falla, limpiar archivo corrupto
                                if (file_exists($fullPath)) {
                                    @unlink($fullPath);
                                }
                                throw new \Exception('Error al guardar el documento: ' . $e->getMessage());
                            }
                            
                            // Liberar memoria del documento principal
                            unset($phpWord, $section);

                            // Crear registro en la base de datos
                            ReporteWord::create([
                                'user_id' => auth()->id(),
                                'owner_id' => $data['owner_id'],
                                'vessel_id' => $data['vessel_id'],
                                'inspector_name' => $data['inspector_name'],
                                'inspection_date' => $data['inspection_date'],
                                'filters' => $data,
                                'file_path' => $filePath,
                            ]);

                            \Filament\Notifications\Notification::make()
                                ->title('Informe generado exitosamente')
                                ->body("El archivo se guardó como: $fileName (Tamaño: " . number_format(filesize($fullPath) / 1024, 2) . " KB)")
                                ->success()
                                ->send();
                                
                        } catch (\Throwable $e) {
                            \Log::error('Error generando informe Word: ' . $e->getMessage());
                            \Log::error('Stack trace: ' . $e->getTraceAsString());
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Error al generar el informe')
                                ->body('Detalles: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('generatePdfReport')
                    ->label('Generar PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->form([
                        Select::make('owner_id')->label('Propietario')->relationship('owner', 'name')->required(),
                        Select::make('vessel_id')->label('Embarcación 1')->relationship('vessel', 'name')->required(),
                        Select::make('vessel2_id')->label('Embarcación 2')->relationship('vessel2', 'name'),
                        Select::make('vessel3_id')->label('Embarcación 3')->relationship('vessel3', 'name'),
                        DatePicker::make('inspection_date')->label('Fecha de Inspección')->required(),
                        TextInput::make('inspector_name')->label('Nombre del Inspector')->required(),
                    ])
                    ->action(function (array $data) {
                        try {
                            // Configurar límites de memoria y tiempo
                            ini_set('memory_limit', '256M');
                            set_time_limit(120);
                            
                            // Preparar datos para la vista
                            $reportData = self::prepareDataForPdf($data);
                            
                            // Generar PDF usando dompdf
                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf-template', $reportData);
                            
                            // Configurar el PDF
                            $pdf->setPaper('A4', 'portrait');
                            
                            // Generar nombre de archivo
                            $fileName = 'reporte_pdf_' . now()->format('Ymd_His') . '.pdf';
                            $directory = storage_path('app/public/reports');
                            if (!file_exists($directory)) {
                                mkdir($directory, 0755, true);
                            }
                            $filePath = 'reports/' . $fileName;
                            $fullPath = storage_path('app/public/' . $filePath);
                            
                            // Guardar el PDF
                            $pdf->save($fullPath);
                            
                            // Crear registro en la base de datos
                            ReporteWord::create([
                                'user_id' => auth()->id(),
                                'owner_id' => $data['owner_id'],
                                'vessel_id' => $data['vessel_id'],
                                'inspector_name' => $data['inspector_name'],
                                'inspection_date' => $data['inspection_date'],
                                'filters' => $data,
                                'file_path' => $filePath,
                            ]);

                            \Filament\Notifications\Notification::make()
                                ->title('Reporte PDF generado exitosamente')
                                ->body("El archivo se guardó como: $fileName")
                                ->success()
                                ->send();
                                
                        } catch (\Throwable $e) {
                            \Log::error('Error generando reporte PDF: ' . $e->getMessage());
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Error al generar el reporte PDF')
                                ->body('Detalles: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    private static function prepareDataForPdf(array $data): array
    {
        try {
            // Datos básicos
            $reportData = [
                'fecha' => \Carbon\Carbon::parse($data['inspection_date'])->format('d/m/Y'),
                'inspector' => self::cleanText($data['inspector_name'] ?? 'No especificado'),
                'propietario' => 'No disponible',
                'embarcacion' => 'No disponible',
                'structureData' => [],
                'certificateData' => [],
                'documentData' => [],
                'crewData' => [],
                'generalObservations' => '',
            ];

            // Obtener información de propietario
            try {
                $owner = \App\Models\Owner::find($data['owner_id']);
                if ($owner && $owner->name) {
                    $reportData['propietario'] = self::cleanText($owner->name);
                }
            } catch (\Exception $e) {
                \Log::error('Error obteniendo propietario: ' . $e->getMessage());
            }

            // Obtener información de embarcación
            try {
                $vessel = \App\Models\Vessel::find($data['vessel_id']);
                if ($vessel && $vessel->name) {
                    $reportData['embarcacion'] = self::cleanText($vessel->name);
                }
            } catch (\Exception $e) {
                \Log::error('Error obteniendo embarcación: ' . $e->getMessage());
            }

            // Recopilar datos de los módulos
            $models = [
                'structure' => \App\Models\StructureAndMachinery::class,
                'certificate' => \App\Models\StatutoryCertificate::class,
                'document' => \App\Models\OnboardManagementDocument::class,
                'crew' => \App\Models\CrewMember::class,
            ];

            foreach ($models as $key => $modelClass) {
                try {
                    if (!class_exists($modelClass)) {
                        continue;
                    }

                    $query = $modelClass::query()
                        ->where('owner_id', $data['owner_id'])
                        ->where('vessel_id', $data['vessel_id'])
                        ->whereDate('inspection_date', $data['inspection_date']);

                    $records = $query->get();

                    if ($records && $records->isNotEmpty()) {
                        foreach ($records as $record) {
                            if ($record) {
                                $itemData = [
                                    'name' => self::cleanText($record->name ?? 'Sin nombre'),
                                    'observations' => self::cleanText($record->observations ?? ''),
                                ];

                                switch ($key) {
                                    case 'structure':
                                        $reportData['structureData'][] = $itemData;
                                        break;
                                    case 'certificate':
                                        $reportData['certificateData'][] = $itemData;
                                        break;
                                    case 'document':
                                        $reportData['documentData'][] = $itemData;
                                        break;
                                    case 'crew':
                                        $reportData['crewData'][] = $itemData;
                                        break;
                                }

                                // Recopilar observaciones generales
                                if (!empty($record->general_observations)) {
                                    $reportData['generalObservations'] .= self::cleanText($record->general_observations) . "\n";
                                }
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    \Log::error("Error procesando datos para PDF ($key): " . $e->getMessage());
                    continue;
                }
            }

            // Limpiar observaciones generales
            $reportData['generalObservations'] = trim($reportData['generalObservations']);

            return $reportData;

        } catch (\Throwable $e) {
            \Log::error('Error preparando datos para PDF: ' . $e->getMessage());
            
            // Retornar datos básicos en caso de error
            return [
                'fecha' => \Carbon\Carbon::parse($data['inspection_date'])->format('d/m/Y'),
                'inspector' => $data['inspector_name'] ?? 'No especificado',
                'propietario' => 'Error al cargar',
                'embarcacion' => 'Error al cargar',
                'structureData' => [],
                'certificateData' => [],
                'documentData' => [],
                'crewData' => [],
                'generalObservations' => 'Error al cargar observaciones',
            ];
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ReporteWordResource\Pages\ListReporteWords::route('/'),
        ];
    }
}
