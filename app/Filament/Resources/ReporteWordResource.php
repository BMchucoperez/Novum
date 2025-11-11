<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use App\Models\ChecklistInspection;
use App\Models\ReporteWord;
use App\Models\Owner;
use App\Models\Vessel;
use Illuminate\Support\Facades\Log;
use App\Filament\Resources\ReporteWordResource\Pages;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteWordResource extends Resource
{
    protected static ?string $model = ReporteWord::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Reportes';

    protected static ?string $navigationLabel = 'Reporte General';

    protected static ?string $modelLabel = 'Reporte';

    protected static ?string $pluralModelLabel = 'Reportes';

    protected static ?int $navigationSort = 1;

    /**
     * Filtrar consultas según el rol del usuario
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        // Si el usuario tiene el rol "Armador", solo mostrar reportes de sus embarcaciones asignadas
        if (Auth::user() && Auth::user()->hasRole('Armador')) {
            $query->whereHas('checklistInspection.vessel', function (Builder $query) {
                $query->where('user_id', Auth::id());
            });
        }
        
        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('checklist_inspection_id')
                    ->label('Inspección Checklist')
                    ->options(function () {
                        $query = ChecklistInspection::with(['owner', 'vessel']);
                        
                        // Si el usuario tiene el rol "Armador", solo mostrar inspecciones de sus embarcaciones asignadas
                        if (Auth::user() && Auth::user()->hasRole('Armador')) {
                            $query->whereHas('vessel', function (Builder $query) {
                                $query->where('user_id', Auth::id());
                            });
                        }
                        
                        return $query->get()
                            ->mapWithKeys(function ($inspection) {
                                return [
                                    $inspection->id => $inspection->owner->name . ' - ' . $inspection->vessel->name . ' (' . $inspection->inspection_start_date->format('d/m/Y') . ')'
                                ];
                            });
                    })
                    ->searchable()
                    ->required()
                    ->live()
                    ->columnSpanFull(),

                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('generate_report')
                        ->label('Generar Reporte')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function ($livewire) {
                            try {
                                // Obtener el valor del campo seleccionado directamente del formulario
                                $checklistInspectionId = $livewire->data['checklist_inspection_id'];

                                if (empty($checklistInspectionId)) {
                                    Notification::make()
                                        ->danger()
                                        ->title('Error')
                                        ->body('Por favor, seleccione una inspección checklist primero.')
                                        ->send();
                                    return;
                                }

                                // Verificar que la inspección existe
                                $inspection = ChecklistInspection::with(['owner', 'vessel'])->find($checklistInspectionId);
                                if (!$inspection) {
                                    Notification::make()
                                        ->danger()
                                        ->title('Error')
                                        ->body('La inspección seleccionada no existe.')
                                        ->send();
                                    return;
                                }

                                // Generar el reporte Word
                                $reportPath = self::generateWordReport($checklistInspectionId);

                                // Verificar si hubo un error en la generación del reporte Word
                                if ($reportPath === null) {
                                    return; // El error ya fue manejado en generateWordReport
                                }

                                // Generar el reporte PDF
                                $ownerName = str_replace(' ', '_', $inspection->owner->name);
                                $vesselName = str_replace(' ', '_', $inspection->vessel->name);
                                $pdfPath = self::generatePDFReport($checklistInspectionId, $ownerName, $vesselName);

                                // Crear un nuevo registro de ReporteWord
                                $reporteWord = new ReporteWord();
                                $reporteWord->checklist_inspection_id = $checklistInspectionId;
                                $reporteWord->user_id = Auth::id();
                                $reporteWord->owner_id = $inspection->owner_id;
                                $reporteWord->vessel_id = $inspection->vessel_id;
                                $reporteWord->inspector_name = $inspection->inspector_name;
                                $reporteWord->inspection_date = $inspection->inspection_start_date;
                                $reporteWord->file_path = $reportPath;
                                $reporteWord->report_path = $reportPath;
                                $reporteWord->pdf_path = $pdfPath;
                                $reporteWord->generated_by = Auth::user()->name;
                                $reporteWord->generated_at = now();
                                $reporteWord->save();

                                // Mostrar notificación de éxito
                                \Filament\Notifications\Notification::make()
                                    ->title('Reportes generados exitosamente')
                                    ->body('Los reportes Word y PDF han sido generados y guardados correctamente.')
                                    ->success()
                                    ->send();

                                // Redireccionar al índice
                                return redirect()->route('filament.admin.resources.reporte-words.index');
                                
                            } catch (\Exception $e) {
                                Log::error('Error en la generación del reporte: ' . $e->getMessage(), [
                                    'checklist_inspection_id' => $checklistInspectionId ?? null,
                                    'user_id' => Auth::id(),
                                    'trace' => $e->getTraceAsString()
                                ]);
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('Error al generar el reporte')
                                    ->body('Ocurrió un error: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Generar Reporte')
                        ->modalDescription('¿Está seguro de que desea generar el reporte Word para esta inspección?')
                        ->modalSubmitActionLabel('Generar Reporte')
                ])
                ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('checklistInspection.owner.name')
                    ->label('Propietario')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('checklistInspection.vessel.name')
                    ->label('Embarcación')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('checklistInspection.inspection_start_date')
                    ->label('Fecha de Inspección')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('generated_by')
                    ->label('Generado por')
                    ->searchable(),

                Tables\Columns\TextColumn::make('generated_at')
                    ->label('Fecha de Generación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('report_path')
                    ->label('Archivo')
                    ->formatStateUsing(fn (string $state): string => basename($state))
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('file_status')
                    ->label('Estado del Archivo')
                    ->state(function (ReporteWord $record): string {
                        $fullPath = storage_path('app/private/' . $record->report_path);
                        if (file_exists($fullPath)) {
                            $fileSize = filesize($fullPath);
                            return 'Disponible (' . round($fileSize / 1024, 1) . ' KB)';
                        }
                        return 'Archivo Faltante';
                    })
                    ->badge()
                    ->color(function (ReporteWord $record): string {
                        $fullPath = storage_path('app/private/' . $record->report_path);
                        return file_exists($fullPath) ? 'success' : 'danger';
                    })
                    ->icon(function (ReporteWord $record): string {
                        $fullPath = storage_path('app/private/' . $record->report_path);
                        return file_exists($fullPath) ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle';
                    }),
            ])
            ->filters([
                Tables\Filters\Filter::make('owner_vessel_filter')
                    ->form([
                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\Select::make('owner_id')
                                    ->label('Propietario')
                                    ->options(Owner::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set) {
                                        $set('vessel_id', null);
                                    }),
                                    
                                Forms\Components\Select::make('vessel_id')
                                    ->label('Embarcación')
                                    ->options(function (Forms\Get $get) {
                                        $ownerId = $get('owner_id');
                                        
                                        $query = Vessel::query();
                                        
                                        // Filtrar por propietario si está seleccionado
                                        if ($ownerId) {
                                            $query->where('owner_id', $ownerId);
                                        }
                                        
                                        // For Armador users, only show vessels assigned to their user account
                                        if (auth()->user() && auth()->user()->hasRole('Armador')) {
                                            $userId = auth()->id();
                                            $query->where('user_id', $userId);
                                        }
                                        
                                        return $query->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn (Forms\Get $get): bool => !$get('owner_id'))
                                    ->helperText('Primero seleccione un propietario'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['owner_id'],
                                fn (Builder $query, $ownerId): Builder => $query->whereHas('checklistInspection', function (Builder $q) use ($ownerId) {
                                    $q->where('owner_id', $ownerId);
                                }),
                            )
                            ->when(
                                $data['vessel_id'],
                                fn (Builder $query, $vesselId): Builder => $query->whereHas('checklistInspection', function (Builder $q) use ($vesselId) {
                                    $q->where('vessel_id', $vesselId);
                                }),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        
                        if ($data['owner_id'] ?? null) {
                            $owner = Owner::find($data['owner_id']);
                            $indicators['owner_id'] = 'Propietario: ' . $owner?->name;
                        }
                        
                        if ($data['vessel_id'] ?? null) {
                            $vessel = Vessel::find($data['vessel_id']);
                            $indicators['vessel_id'] = 'Embarcación: ' . $vessel?->name;
                        }
                        
                        return $indicators;
                    }),
            ])
            // ->headerActions([
            //     Tables\Actions\Action::make('cleanup')
            //         ->label('Limpiar Archivos Huérfanos')
            //         ->icon('heroicon-o-trash')
            //         ->color('warning')
            //         ->action(function () {
            //             $cleaned = 0;
            //             $reports = ReporteWord::all();
                        
            //             foreach ($reports as $report) {
            //                 $fullPath = storage_path('app/private/' . $report->report_path);
            //                 if (!file_exists($fullPath)) {
            //                     $report->delete();
            //                     $cleaned++;
            //                 }
            //             }
                        
            //             Notification::make()
            //                 ->success()
            //                 ->title('Limpieza completada')
            //                 ->body("Se eliminaron {$cleaned} registros sin archivo.")
            //                 ->send();
            //         })
            //         ->requiresConfirmation()
            //         ->modalHeading('Limpiar registros sin archivo')
            //         ->modalDescription('¿Está seguro de que desea eliminar los registros de reportes cuyos archivos ya no existen?')
            //         ->modalSubmitActionLabel('Limpiar'),
            // ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Descargar Word')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->url(fn (ReporteWord $record): string => url('/reporte-word/download/' . $record->id))
                    ->openUrlInNewTab()
                    ->hidden(),

                Tables\Actions\Action::make('download_pdf')
                    ->label('Descargar PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->url(fn (ReporteWord $record): string => url('/reporte-word/download-pdf/' . $record->id))
                    ->openUrlInNewTab()
                    ->visible(function (ReporteWord $record): bool {
                        // Only show download button if PDF exists
                        if (empty($record->pdf_path)) {
                            return false;
                        }
                        $fullPath = storage_path('app/private/' . $record->pdf_path);
                        return file_exists($fullPath);
                    }),

                Tables\Actions\Action::make('file_missing')
                    ->label('Archivo Faltante')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->disabled()
                    ->visible(function (ReporteWord $record): bool {
                        // Show warning if file doesn't exist
                        $fullPath = storage_path('app/private/' . $record->report_path);
                        return !file_exists($fullPath);
                    }),
                
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReporteWords::route('/'),
            'create' => Pages\CreateReporteWord::route('/create'),
            'view' => Pages\ViewReporteWord::route('/{record}'),
            'edit' => Pages\EditReporteWord::route('/{record}/edit'),
        ];
    }
    
    protected static function generateWordReport($checklistInspectionId)
    {
        try {
            $inspection = ChecklistInspection::with(['owner', 'vessel'])->find($checklistInspectionId);
            
            if (!$inspection || !$inspection->owner || !$inspection->vessel) {
                Notification::make()
                    ->danger()
                    ->title('Error')
                    ->body('Datos de inspección incompletos.')
                    ->send();
                return null;
            }
            
            // CONFIGURACIONES CRÍTICAS para prevenir corrupción según documentación PHPWord 1.4.0
            // 1. Habilitar output escaping (CRÍTICO para caracteres especiales)
            \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
            
            // 2. Configurar compatibilidad XML (requerido para OpenOffice/LibreOffice)
            \PhpOffice\PhpWord\Settings::setCompatibility(true);
            
            // Crear archivo temporal usando la técnica correcta
            $tempPath = tempnam(sys_get_temp_dir(), 'pw_') . '.docx';
            
            $phpWord = new PhpWord();
            
            // 3. Configurar propiedades del documento para prevenir errores
            $phpWord->getSettings()->setHideGrammaticalErrors(true);
            $phpWord->getSettings()->setHideSpellingErrors(true);
            
            // 4. Configurar fuentes por defecto del documento
            $phpWord->setDefaultFontName('Calibri');
            $phpWord->setDefaultFontSize(11);
            
            // 5. Definir esquema de colores corporativo
            $colorScheme = [
                'primary' => '1F4E79',      // Azul marino corporativo
                'secondary' => '2E75B6',    // Azul medio
                'accent' => '5B9BD5',       // Azul claro
                'text' => '1F1F1F',         // Gris oscuro
                'lightGray' => 'F2F2F2',    // Gris muy claro
                'mediumGray' => 'D9D9D9',   // Gris medio
                'success' => '70AD47',      // Verde
                'warning' => 'FFC000',      // Amarillo
                'danger' => 'C5504B'        // Rojo
            ];
            
            // 6. Definir estilos de fuente personalizados
            $phpWord->addFontStyle('titleStyle', [
                'name' => 'Calibri',
                'size' => 20,
                'bold' => true,
                'color' => $colorScheme['primary'],
                'allCaps' => true
            ]);
            
            $phpWord->addFontStyle('headerStyle', [
                'name' => 'Calibri',
                'size' => 14,
                'bold' => true,
                'color' => $colorScheme['secondary']
            ]);
            
            $phpWord->addFontStyle('subHeaderStyle', [
                'name' => 'Calibri',
                'size' => 12,
                'bold' => true,
                'color' => $colorScheme['text']
            ]);
            
            $phpWord->addFontStyle('normalStyle', [
                'name' => 'Calibri',
                'size' => 11,
                'color' => $colorScheme['text']
            ]);
            
            $phpWord->addFontStyle('emphasisStyle', [
                'name' => 'Calibri',
                'size' => 11,
                'italic' => true,
                'color' => $colorScheme['secondary']
            ]);
            
            $phpWord->addFontStyle('whiteStyle', [
                'name' => 'Calibri',
                'size' => 11,
                'bold' => true,
                'color' => 'FFFFFF'
            ]);
            
            // 7. Definir estilos de párrafo
            $phpWord->addParagraphStyle('titleParagraph', [
                'alignment' => 'center',
                'spaceBefore' => 0,
                'spaceAfter' => 400,
                'borderBottomSize' => 18,
                'borderBottomColor' => $colorScheme['primary']
            ]);
            
            $phpWord->addParagraphStyle('headerParagraph', [
                'alignment' => 'left',
                'spaceBefore' => 300,
                'spaceAfter' => 200
            ]);
            
            $phpWord->addParagraphStyle('normalParagraph', [
                'alignment' => 'left',
                'spaceBefore' => 120,
                'spaceAfter' => 120,
                'lineHeight' => 1.2
            ]);
            
            // 8. Definir estilos de tabla
            $phpWord->addTableStyle('infoTable', [
                'borderSize' => 6,
                'borderColor' => $colorScheme['mediumGray'],
                'cellMargin' => 100,
                'width' => 100 * 50, // 100% width
                'unit' => 'pct'
            ], [
                'bgColor' => $colorScheme['primary']
            ]);
            
            $phpWord->addTableStyle('checklistTable', [
                'borderSize' => 6,
                'borderColor' => $colorScheme['mediumGray'],
                'cellMargin' => 80,
                'width' => 100 * 50,
                'unit' => 'pct'
            ], [
                'bgColor' => $colorScheme['secondary']
            ]);
            
            // 9. Crear sección con márgenes personalizados
            $sectionStyle = [
                'marginTop' => 1440,    // 1 inch
                'marginBottom' => 1440,
                'marginLeft' => 1440,
                'marginRight' => 1440,
                'headerHeight' => 720,
                'footerHeight' => 720
            ];
            $section = $phpWord->addSection($sectionStyle);
            
            // 10. Agregar header profesional
            $header = $section->addHeader();
            $headerTable = $header->addTable();
            $headerTable->addRow();
            $headerTable->addCell(8000)->addText('SISTEMA DE INSPECCIÓN MARÍTIMA', 'headerStyle');
            $headerTable->addCell(2000)->addText(date('d/m/Y'), 'normalStyle', ['alignment' => 'right']);
            
            // 11. Contenido principal del reporte con diseño profesional
            $section->addText('INFORME DE INSPECCIÓN CHECKLIST', 'titleStyle', 'titleParagraph');
            $section->addTextBreak(2);
            
            // 12. Tabla de información general
            $section->addText('INFORMACIÓN GENERAL', 'headerStyle', 'headerParagraph');
            $section->addTextBreak(1);
            
            $infoTable = $section->addTable('infoTable');
            
            // Fila de encabezado
            $infoTable->addRow();
            $infoTable->addCell(3000, ['bgColor' => $colorScheme['primary']])->addText('Campo', 'whiteStyle');
            $infoTable->addCell(7000, ['bgColor' => $colorScheme['primary']])->addText('Información', 'whiteStyle');
            
            // Datos de la inspección
            // Convertir el estado general a su descripción completa según las especificaciones
            $estadoGeneral = $inspection->overall_status ?? 'APTO';
            $estadoGeneralDescripcion = match($estadoGeneral) {
                'APTO' => 'APTO',
                'NO APTO' => 'NO APTO',
                'OBSERVADO' => 'OBSERVADO',
                'V' => 'APTO', // Compatibilidad con códigos antiguos
                'A' => 'OBSERVADO',
                'N' => 'OBSERVADO', 
                'R' => 'NO APTO',
                default => $estadoGeneral
            };
            
            $infoData = [
                'Propietario' => htmlspecialchars($inspection->owner->name, ENT_QUOTES, 'UTF-8'),
                'Embarcación' => htmlspecialchars($inspection->vessel->name, ENT_QUOTES, 'UTF-8'),
                'Inspector' => htmlspecialchars($inspection->inspector_name, ENT_QUOTES, 'UTF-8'),
                'Fecha de Inicio' => $inspection->inspection_start_date->format('d/m/Y'),
                'Fecha de Fin' => $inspection->inspection_end_date ? $inspection->inspection_end_date->format('d/m/Y') : 'N/A',
                'Estado General' => htmlspecialchars($estadoGeneralDescripcion, ENT_QUOTES, 'UTF-8')
            ];
            
            foreach ($infoData as $field => $value) {
                $infoTable->addRow();
                $infoTable->addCell(3000, ['bgColor' => $colorScheme['lightGray']])->addText($field, 'subHeaderStyle');
                $infoTable->addCell(7000)->addText($value, 'normalStyle');
            }
            $section->addTextBreak(2);
            
            // 13. Partes del checklist con extracción mejorada de datos
            $parteTitles = [
                1 => 'PARTE 1: DOCUMENTOS DE BANDERA Y PÓLIZAS DE SEGURO',
                2 => 'PARTE 2: DOCUMENTOS DEL SISTEMA DE GESTIÓN DE BORDO',
                3 => 'PARTE 3: CASCO Y ESTRUCTURAS - INSPECCIÓN VISUAL',
                4 => 'PARTE 4: SISTEMAS DE SEGURIDAD Y OPERACIONALES',
                5 => 'PARTE 5: EQUIPOS DE NAVEGACIÓN Y SEÑALIZACIÓN',
                6 => 'PARTE 6: SISTEMAS DE AMARRE Y CONEXIONES'
            ];
            
            for ($i = 1; $i <= 6; $i++) {
                $section->addText($parteTitles[$i], 'headerStyle', 'headerParagraph');
                $section->addTextBreak(1);
                
                // Obtener y validar datos del checklist
                $items = $inspection->{"parte_{$i}_items"};
                
                // Validación robusta de datos
                if (!is_array($items) || empty($items)) {
                    $section->addText('❌ Sin items registrados para esta parte', 'emphasisStyle', 'normalParagraph');
                    $section->addTextBreak(2);
                    continue;
                }
                
                // Crear tabla extendida para todos los campos del checklist
                $checklistTable = $section->addTable('checklistTable');
                
                // Encabezado de la tabla con más columnas
                $checklistTable->addRow();
                $checklistTable->addCell(800, ['bgColor' => $colorScheme['secondary']])->addText('#', 'whiteStyle');
                $checklistTable->addCell(4000, ['bgColor' => $colorScheme['secondary']])->addText('Ítem de Inspección', 'whiteStyle');
                $checklistTable->addCell(1000, ['bgColor' => $colorScheme['secondary']])->addText('Prioridad', 'whiteStyle');
                $checklistTable->addCell(1200, ['bgColor' => $colorScheme['secondary']])->addText('Estado', 'whiteStyle');
                $checklistTable->addCell(1000, ['bgColor' => $colorScheme['secondary']])->addText('Archivos', 'whiteStyle');
                $checklistTable->addCell(3000, ['bgColor' => $colorScheme['secondary']])->addText('Observaciones', 'whiteStyle');
                
                // Contador de items válidos
                $itemCount = 0;
                
                foreach ($items as $index => $item) {
                    // Validar estructura del item
                    if (!is_array($item)) {
                        continue;
                    }
                    
                    $itemCount++;
                    
                    // Extraer todos los campos con validación mejorada
                    $itemText = htmlspecialchars($item['item'] ?? 'Ítem no especificado', ENT_QUOTES, 'UTF-8');
                    
                    // CORRECCIÓN MEJORADA: Obtener el estado real del formulario con todas las verificaciones
                    $estadoRaw = $item['estado'] ?? '';
                    $estadoText = trim($estadoRaw);
                    
                    // Si el estado está vacío o es null, verificar los checkboxes según la lógica del sistema
                    if (empty($estadoText)) {
                        $checkbox1 = $item['checkbox_1'] ?? false;
                        $checkbox2 = $item['checkbox_2'] ?? false;
                        
                        // Checkbox2 es el definitivo según ChecklistInspectionResource línea 346-362
                        if ($checkbox2) {
                            $estadoText = 'V'; // Checkbox2 marcado = Estado V automático
                        } elseif ($checkbox1) {
                            $estadoText = 'Verificado';
                        } else {
                            $estadoText = 'Sin evaluar';
                        }
                    }
                    
                    $prioridad = $item['prioridad'] ?? 3;
                    
                    // CORRECCIÓN MEJORADA: Obtener comentarios/observaciones con múltiples variantes de campo
                    $comentarios = '';
                    // Verificar diferentes nombres de campo para comentarios
                    if (!empty($item['comentarios'])) {
                        $comentarios = $item['comentarios'];
                    } elseif (!empty($item['observaciones'])) {
                        $comentarios = $item['observaciones'];
                    } elseif (!empty($item['comments'])) {
                        $comentarios = $item['comments'];
                    }
                    $comentarios = htmlspecialchars($comentarios, ENT_QUOTES, 'UTF-8');
                    
                    // CORRECCIÓN MEJORADA: Obtener archivos adjuntos reales del sistema con validación más robusta
                    $archivosAdjuntos = $item['archivos_adjuntos'] ?? [];
                    
                    // Los archivos se pueden guardar como array de rutas o como array de objetos
                    $archivosValidos = [];
                    $totalSizeBytes = 0;
                    
                    if (is_array($archivosAdjuntos)) {
                        foreach ($archivosAdjuntos as $archivo) {
                            $rutaArchivo = '';
                            
                            // Manejar diferentes formatos de archivos adjuntos
                            if (is_string($archivo)) {
                                $rutaArchivo = $archivo;
                            } elseif (is_array($archivo) && isset($archivo['path'])) {
                                $rutaArchivo = $archivo['path'];
                            } elseif (is_array($archivo) && isset($archivo['file'])) {
                                $rutaArchivo = $archivo['file'];
                            }
                            
                            if (!empty($rutaArchivo)) {
                                // Verificar si el archivo existe físicamente
                                $rutaCompleta = storage_path('app/private/' . $rutaArchivo);
                                if (file_exists($rutaCompleta)) {
                                    $archivosValidos[] = [
                                        'ruta' => $rutaArchivo,
                                        'nombre' => basename($rutaArchivo),
                                        'size' => filesize($rutaCompleta)
                                    ];
                                    $totalSizeBytes += filesize($rutaCompleta);
                                }
                            }
                        }
                    }
                    
                    // Determinar color según el estado del sistema ChecklistInspection
                    $estadoColor = $colorScheme['mediumGray'];
                    $estadoFont = 'normalStyle';
                    $estadoDescripcion = $estadoText;
                    
                    // Convertir estado según la lógica del checklist
                    $estadoFinal = '';
                    switch (strtoupper(trim($estadoText))) {
                        case 'V':
                            $estadoFinal = 'APTO';
                            $estadoColor = $colorScheme['success'];
                            $estadoFont = 'whiteStyle';
                            break;
                        case 'A':
                            $estadoFinal = 'OBSERVADO';
                            $estadoColor = $colorScheme['warning'];
                            break;
                        case 'N':
                            $estadoFinal = 'OBSERVADO';
                            $estadoColor = $colorScheme['danger'];
                            $estadoFont = 'whiteStyle';
                            break;
                        case 'R':
                            $estadoFinal = 'NO APTO';
                            $estadoColor = $colorScheme['danger'];
                            $estadoFont = 'whiteStyle';
                            break;
                        case 'APTO':
                            $estadoFinal = 'APTO';
                            $estadoColor = $colorScheme['success'];
                            $estadoFont = 'whiteStyle';
                            break;
                        case 'OBSERVADO':
                            $estadoFinal = 'OBSERVADO';
                            $estadoColor = $colorScheme['warning'];
                            break;
                        case 'NO APTO':
                            $estadoFinal = 'NO APTO';
                            $estadoColor = $colorScheme['danger'];
                            $estadoFont = 'whiteStyle';
                            break;
                        case 'VERIFICADO':
                            $estadoFinal = 'APTO';
                            $estadoColor = $colorScheme['success'];
                            $estadoFont = 'whiteStyle';
                            break;
                        case 'SIN EVALUAR':
                        case '':
                            $estadoFinal = 'Sin evaluar';
                            $estadoColor = $colorScheme['lightGray'];
                            break;
                        default:
                            $estadoFinal = htmlspecialchars($estadoText, ENT_QUOTES, 'UTF-8');
                            $estadoColor = $colorScheme['lightGray'];
                    }
                    
                    // Formatear la descripción del estado según las especificaciones
                    switch ($estadoFinal) {
                        case 'APTO':
                            $estadoDescripcion = 'APTO - Cumple con los requisitos';
                            break;
                        case 'NO APTO':
                            $estadoDescripcion = 'NO APTO - No cumple (Prioridad 1)';
                            break;
                        case 'OBSERVADO':
                            $estadoDescripcion = 'OBSERVADO - No cumple (Prioridad 2-3)';
                            break;
                        case 'Sin evaluar':
                            $estadoDescripcion = 'Sin evaluar';
                            break;
                        default:
                            $estadoDescripcion = $estadoFinal;
                    }
                    
                    // Determinar color y texto de prioridad
                    $prioridadColor = $colorScheme['mediumGray'];
                    $prioridadFont = 'normalStyle';
                    $prioridadText = 'Media';
                    
                    switch ($prioridad) {
                        case 1:
                            $prioridadColor = $colorScheme['danger'];
                            $prioridadFont = 'whiteStyle';
                            $prioridadText = 'Crítica';
                            break;
                        case 2:
                            $prioridadColor = $colorScheme['warning'];
                            $prioridadText = 'Alta';
                            break;
                        case 3:
                            $prioridadColor = $colorScheme['lightGray'];
                            $prioridadText = 'Media';
                            break;
                    }
                    
                    // Información de archivos adjuntos REALES con detalles mejorados
                    $archivosText = '📄 Sin archivos';
                    $archivosColor = $colorScheme['lightGray'];
                    
                    if (!empty($archivosValidos)) {
                        $cantidadArchivos = count($archivosValidos);
                        $archivosText = "📎 {$cantidadArchivos} archivo" . ($cantidadArchivos > 1 ? 's' : '') . ' adjunto' . ($cantidadArchivos > 1 ? 's' : '');
                        $archivosColor = $colorScheme['accent'];
                        
                        // Agregar tamaños de archivo con formato mejorado
                        if ($totalSizeBytes > 0) {
                            if ($totalSizeBytes < 1024) {
                                $sizeText = $totalSizeBytes . ' bytes';
                            } elseif ($totalSizeBytes < 1024 * 1024) {
                                $sizeText = round($totalSizeBytes / 1024, 1) . ' KB';
                            } else {
                                $sizeText = round($totalSizeBytes / (1024 * 1024), 1) . ' MB';
                            }
                            $archivosText .= " ({$sizeText})";
                        }
                        
                        // Añadir información sobre tipos de archivo si hay variedad
                        $extensiones = [];
                        foreach ($archivosValidos as $archivo) {
                            $ext = strtoupper(pathinfo($archivo['nombre'], PATHINFO_EXTENSION));
                            if (!empty($ext) && !in_array($ext, $extensiones)) {
                                $extensiones[] = $ext;
                            }
                        }
                        if (count($extensiones) <= 3 && !empty($extensiones)) {
                            $archivosText .= ' [' . implode(', ', $extensiones) . ']';
                        }
                    }
                    
                    // Agregar fila con datos completos y corregidos
                    $checklistTable->addRow();
                    $checklistTable->addCell(800)->addText($itemCount, 'normalStyle');
                    $checklistTable->addCell(4000)->addText($itemText, 'normalStyle');
                    $checklistTable->addCell(1000, ['bgColor' => $prioridadColor])->addText($prioridadText, $prioridadFont);
                    $checklistTable->addCell(1200, ['bgColor' => $estadoColor])->addText($estadoDescripcion, $estadoFont);
                    $checklistTable->addCell(1000, ['bgColor' => $archivosColor])->addText($archivosText, 'normalStyle');
                    $checklistTable->addCell(3000)->addText($comentarios ?: 'Sin observaciones', 'normalStyle');
                }
                
                // Agregar resumen de la parte
                if ($itemCount > 0) {
                    $section->addTextBreak(1);
                    $resumenTable = $section->addTable([
                        'borderSize' => 6,
                        'borderColor' => $colorScheme['mediumGray'],
                        'cellMargin' => 80,
                        'width' => 100 * 50,
                        'unit' => 'pct'
                    ]);
                    
                    $resumenTable->addRow();
                    $resumenTable->addCell(10000, ['bgColor' => $colorScheme['lightGray']])
                        ->addText("📊 Resumen: {$itemCount} ítems evaluados en esta parte", 'emphasisStyle');
                }
                
                $section->addTextBreak(2);
            }
            
            // 14. Resumen estadístico de la inspección
            $section->addText('RESUMEN ESTADÍSTICO DE LA INSPECCIÓN', 'headerStyle', 'headerParagraph');
            $section->addTextBreak(1);
            
            // Calcular estadísticas generales mejoradas
            $totalItems = 0;
            $estadosCounts = ['V' => 0, 'A' => 0, 'N' => 0, 'R' => 0, 'Sin evaluar' => 0, 'Verificado' => 0];
            $prioridadesCounts = [1 => 0, 2 => 0, 3 => 0];
            $totalArchivos = 0;
            $totalSizeBytes = 0;
            
            for ($i = 1; $i <= 6; $i++) {
                $items = $inspection->{"parte_{$i}_items"};
                if (is_array($items)) {
                    foreach ($items as $item) {
                        if (is_array($item)) {
                            $totalItems++;
                            
                            // Procesar estado igual que en la sección principal
                            $estadoRaw = $item['estado'] ?? '';
                            $estadoText = trim($estadoRaw);
                            
                            if (empty($estadoText)) {
                                $checkbox1 = $item['checkbox_1'] ?? false;
                                $checkbox2 = $item['checkbox_2'] ?? false;
                                
                                if ($checkbox2) {
                                    $estadoText = 'V';
                                } elseif ($checkbox1) {
                                    $estadoText = 'Verificado';
                                } else {
                                    $estadoText = 'Sin evaluar';
                                }
                            }
                            
                            // Convertir estado a valor final usando la misma lógica
                            $estadoFinal = '';
                            switch (strtoupper(trim($estadoText))) {
                                case 'V':
                                    $estadoFinal = 'APTO';
                                    break;
                                case 'A':
                                    $estadoFinal = 'OBSERVADO';
                                    break;
                                case 'N':
                                    $estadoFinal = 'OBSERVADO';
                                    break;
                                case 'R':
                                    $estadoFinal = 'NO APTO';
                                    break;
                                case 'APTO':
                                case 'OBSERVADO':
                                case 'NO APTO':
                                    $estadoFinal = strtoupper(trim($estadoText));
                                    break;
                                case 'VERIFICADO':
                                    $estadoFinal = 'APTO';
                                    break;
                                case 'SIN EVALUAR':
                                case '':
                                    $estadoFinal = 'Sin evaluar';
                                    break;
                                default:
                                    $estadoFinal = 'APTO'; // Valor por defecto
                            }
                            
                            $prioridad = $item['prioridad'] ?? 3;
                            $archivos = $item['archivos_adjuntos'] ?? [];
                            
                            // Contar estados correctamente usando el valor final
                            $estadosCounts[$estadoFinal] = ($estadosCounts[$estadoFinal] ?? 0) + 1;
                            $prioridadesCounts[$prioridad] = ($prioridadesCounts[$prioridad] ?? 0) + 1;
                            
                            // Contar archivos válidos
                            if (is_array($archivos)) {
                                foreach ($archivos as $archivo) {
                                    $rutaArchivo = '';
                                    if (is_string($archivo)) {
                                        $rutaArchivo = $archivo;
                                    } elseif (is_array($archivo) && isset($archivo['path'])) {
                                        $rutaArchivo = $archivo['path'];
                                    }
                                    
                                    if (!empty($rutaArchivo)) {
                                        $rutaCompleta = storage_path('app/private/' . $rutaArchivo);
                                        if (file_exists($rutaCompleta)) {
                                            $totalArchivos++;
                                            $totalSizeBytes += filesize($rutaCompleta);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            // Tabla de resumen estadístico
            $resumenTable = $section->addTable('infoTable');
            
            // Encabezado del resumen
            $resumenTable->addRow();
            $resumenTable->addCell(5000, ['bgColor' => $colorScheme['primary']])->addText('Concepto', 'whiteStyle');
            $resumenTable->addCell(5000, ['bgColor' => $colorScheme['primary']])->addText('Cantidad / Porcentaje', 'whiteStyle');
            
            // Total de items
            $resumenTable->addRow();
            $resumenTable->addCell(5000, ['bgColor' => $colorScheme['lightGray']])->addText('Total de Ítems Inspeccionados', 'subHeaderStyle');
            $resumenTable->addCell(5000)->addText($totalItems . ' ítems', 'normalStyle');
            
            // Función auxiliar para convertir estados individuales a valores finales
            $convertEstadoToFinal = function($estado) {
                $estado = strtoupper(trim($estado));
                
                // Aplicar la misma lógica que en ChecklistInspection::calculateOverallStatus()
                switch ($estado) {
                    case 'NO APTO':
                    case 'R':
                        return 'NO APTO';
                    case 'OBSERVADO':
                    case 'N':
                    case 'A':
                        return 'OBSERVADO';
                    case 'APTO':
                    case 'V':
                    case 'VERIFICADO':
                        return 'APTO';
                    case 'SIN EVALUAR':
                    case '':
                        return 'Sin evaluar';
                    default:
                        return 'APTO';
                }
            };
            
            // Estados finales con descripciones
            $estadoDescripciones = [
                'APTO' => 'Cumple con los requisitos',
                'OBSERVADO' => 'No cumple (Prioridad 2-3)',
                'NO APTO' => 'No cumple (Prioridad 1)',
                'Sin evaluar' => 'Sin Evaluar'
            ];
            
            // Agrupar estados por valores finales
            $finalEstadosCounts = [
                'APTO' => 0,
                'OBSERVADO' => 0,
                'NO APTO' => 0,
                'Sin evaluar' => 0
            ];
            
            foreach ($estadosCounts as $estado => $cantidad) {
                $finalEstado = $convertEstadoToFinal($estado);
                $finalEstadosCounts[$finalEstado] += $cantidad;
            }
            
            // Mostrar estados finales
            foreach ($finalEstadosCounts as $finalEstado => $cantidad) {
                // Solo mostrar estados que tienen al menos 1 item
                if ($cantidad > 0) {
                    $porcentaje = $totalItems > 0 ? round(($cantidad / $totalItems) * 100, 1) : 0;
                    $descripcion = $estadoDescripciones[$finalEstado] ?? $finalEstado;
                    
                    $colorFondo = $colorScheme['lightGray'];
                    $fontStyle = 'normalStyle';
                    switch ($finalEstado) {
                        case 'APTO': 
                            $colorFondo = $colorScheme['success']; 
                            $fontStyle = 'whiteStyle';
                            break;
                        case 'OBSERVADO': 
                            $colorFondo = $colorScheme['warning']; 
                            break;
                        case 'NO APTO': 
                            $colorFondo = $colorScheme['danger'];
                            $fontStyle = 'whiteStyle';
                            break;
                        case 'Verificado':
                            $colorFondo = $colorScheme['accent'];
                            break;
                    }
                    
                    $resumenTable->addRow();
                    $resumenTable->addCell(5000, ['bgColor' => $colorScheme['lightGray']])->addText("Estado {$finalEstado} - {$descripcion}", 'subHeaderStyle');
                    $resumenTable->addCell(5000, ['bgColor' => $colorFondo])->addText("{$cantidad} ({$porcentaje}%)", $fontStyle);
                }
            }
            
            // Prioridades
            foreach ([1 => 'Crítica', 2 => 'Alta', 3 => 'Media'] as $nivel => $descripcion) {
                $cantidad = $prioridadesCounts[$nivel] ?? 0;
                $porcentaje = $totalItems > 0 ? round(($cantidad / $totalItems) * 100, 1) : 0;
                
                $resumenTable->addRow();
                $resumenTable->addCell(5000, ['bgColor' => $colorScheme['lightGray']])->addText("Prioridad {$nivel} - {$descripcion}", 'subHeaderStyle');
                $resumenTable->addCell(5000)->addText("{$cantidad} ({$porcentaje}%)", 'normalStyle');
            }
            
            // Total de archivos adjuntos con información mejorada
            $resumenTable->addRow();
            $resumenTable->addCell(5000, ['bgColor' => $colorScheme['lightGray']])->addText('Total de Archivos Adjuntos Válidos', 'subHeaderStyle');
            
            $archivosSizeText = $totalArchivos . ' archivos';
            if ($totalSizeBytes > 0) {
                if ($totalSizeBytes < 1024 * 1024) {
                    $sizeFormatted = round($totalSizeBytes / 1024, 1) . ' KB';
                } else {
                    $sizeFormatted = round($totalSizeBytes / (1024 * 1024), 1) . ' MB';
                }
                $archivosSizeText .= " ({$sizeFormatted})";
            }
            $resumenTable->addCell(5000)->addText($archivosSizeText, 'normalStyle');
            
            $section->addTextBreak(2);
            
            // 16. Observaciones generales con estilo mejorado
            if (!empty($inspection->general_observations)) {
                $section->addText('OBSERVACIONES GENERALES', 'headerStyle', 'headerParagraph');
                $section->addTextBreak(1);
                
                // Crear una caja de texto con fondo
                $observationsTable = $section->addTable([
                    'borderSize' => 6,
                    'borderColor' => $colorScheme['mediumGray'],
                    'cellMargin' => 200,
                    'width' => 100 * 50,
                    'unit' => 'pct'
                ]);
                
                $observationsTable->addRow();
                $cell = $observationsTable->addCell(10000, ['bgColor' => $colorScheme['lightGray']]);
                $observations = htmlspecialchars($inspection->general_observations, ENT_QUOTES, 'UTF-8');
                $cell->addText($observations, 'normalStyle', 'normalParagraph');
                
                $section->addTextBreak(1);
            }
            
            // 17. Agregar footer profesional
            $footer = $section->addFooter();
            $footerTable = $footer->addTable();
            $footerTable->addRow();
            $footerTable->addCell(5000)->addText('Documento generado automáticamente', 'emphasisStyle');
            $footerTable->addCell(5000)->addPreserveText('Página {PAGE} de {NUMPAGES}', 'emphasisStyle', ['alignment' => 'right']);
            
            // 5. Guardar usando IOFactory con configuración robusta
            try {
                $writer = IOFactory::createWriter($phpWord, 'Word2007');
                $writer->save($tempPath);
                
                // Verificar que el archivo se generó correctamente con validaciones más estrictas
                if (!file_exists($tempPath)) {
                    throw new \Exception('Archivo temporal no fue creado');
                }
                
                $fileSize = filesize($tempPath);
                if ($fileSize < 1000) {
                    throw new \Exception('Archivo generado demasiado pequeño (' . $fileSize . ' bytes), posiblemente corrupto');
                }
                
                // Verificar que el archivo es un ZIP válido (los .docx son archivos ZIP)
                $zip = new \ZipArchive();
                if ($zip->open($tempPath, \ZipArchive::CHECKCONS) !== TRUE) {
                    throw new \Exception('El archivo generado no es un documento Word válido');
                }
                $zip->close();
                
            } catch (\Exception $writerException) {
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }
                throw new \Exception('Error al generar el documento Word: ' . $writerException->getMessage());
            }
            
            // Crear nombre descriptivo
            $vesselName = preg_replace('/[^A-Za-z0-9_-]/', '_', $inspection->vessel->name);
            $ownerName = preg_replace('/[^A-Za-z0-9_-]/', '_', $inspection->owner->name);
            $fileName = 'Reporte_' . $ownerName . '_' . $vesselName . '_' . date('Y-m-d_H-i-s') . '.docx';
            $filePath = 'reports/' . $fileName;
            $finalPath = storage_path('app/private/' . $filePath);
            
            // Crear directorio si no existe
            $directory = dirname($finalPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Mover archivo
            if (!rename($tempPath, $finalPath)) {
                throw new \Exception('No se pudo mover el archivo al directorio final');
            }
            
            return $filePath;
            
        } catch (\Exception $e) {
            Log::error('Error generando reporte: ' . $e->getMessage(), [
                'inspection_id' => $checklistInspectionId ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('No se pudo generar el reporte: ' . $e->getMessage())
                ->send();
            
            return null;
        }
    }

    /**
     * Genera el reporte PDF de la inspección
     */
    protected static function generatePDFReport($checklistInspectionId, $ownerName, $vesselName)
    {
        try {
            $inspection = ChecklistInspection::with(['owner', 'vessel'])->find($checklistInspectionId);

            if (!$inspection) {
                throw new \Exception('Inspección no encontrada');
            }

            // Preparar datos para el PDF
            $partes = [];
            $stats = [
                'apto' => 0,
                'no_apto' => 0,
                'observado' => 0,
                'total' => 0,
                'porcentaje_cumplimiento' => 0,
            ];

            // Títulos de las partes (EXACTOS del checklist)
            $parteTitles = [
                1 => 'PARTE 1: DOCUMENTOS DE BANDERA Y PÓLIZAS DE SEGURO',
                2 => 'PARTE 2: SISTEMA DE GESTÃO',
                3 => 'PARTE 3: CASCO E ESTRUTURAS',
                4 => 'PARTE 4: SISTEMAS DE CARGA/DESCARGA',
                5 => 'PARTE 5: SEGURANÇA E LUZES DE NAVEGAÇÃO',
                6 => 'PARTE 6: SISTEMAS DE AMARRAÇÃO',
            ];

            // Recopilar datos de cada parte
            for ($i = 1; $i <= 6; $i++) {
                $items = $inspection->getAttribute('parte_' . $i . '_items') ?? [];

                if (!empty($items)) {
                    $partes[$i] = [
                        'title' => $parteTitles[$i],
                        'items' => $items,
                    ];

                    // Contar estados
                    foreach ($items as $item) {
                        $estado = $item['estado'] ?? '';
                        if (!empty($estado)) {
                            $stats['total']++;
                            if ($estado === 'A') $stats['apto']++;
                            elseif ($estado === 'N') $stats['no_apto']++;
                            elseif ($estado === 'O') $stats['observado']++;
                        }
                    }
                }
            }

            // Calcular porcentaje de cumplimiento
            if ($stats['total'] > 0) {
                $stats['porcentaje_cumplimiento'] = round(($stats['apto'] / $stats['total']) * 100, 1);
            }

            // Generar PDF
            $pdf = Pdf::loadView('pdf.reporte-inspeccion', [
                'inspection' => $inspection,
                'partes' => $partes,
                'stats' => $stats,
            ]);

            // Configurar PDF
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isRemoteEnabled', false);

            // Crear nombre descriptivo
            $fileName = 'Reporte_' . $ownerName . '_' . $vesselName . '_' . date('Y-m-d_H-i-s') . '.pdf';
            $filePath = 'reports/' . $fileName;
            $finalPath = storage_path('app/private/' . $filePath);

            // Crear directorio si no existe
            $directory = dirname($finalPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Guardar PDF
            $pdf->save($finalPath);

            return $filePath;

        } catch (\Exception $e) {
            Log::error('Error generando reporte PDF: ' . $e->getMessage(), [
                'inspection_id' => $checklistInspectionId ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
}