<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReporteWordResource\Pages;
use App\Models\ReporteWord;
use App\Models\ChecklistInspection;
use App\Models\Owner;
use App\Models\Vessel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class ReporteWordResource extends Resource
{
    protected static ?string $model = ReporteWord::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Reportes';

    protected static ?string $navigationLabel = 'Generar Reporte Word';

    protected static ?string $modelLabel = 'Reporte Word';

    protected static ?string $pluralModelLabel = 'Reportes Word';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('checklist_inspection_id')
                    ->label('Inspección Checklist')
                    ->options(function () {
                        return ChecklistInspection::with(['owner', 'vessel'])
                            ->get()
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
                        ->label('Generar Reporte Word')
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
                                
                                // Verificar si hubo un error en la generación del reporte
                                if ($reportPath === null) {
                                    return; // El error ya fue manejado en generateWordReport
                                }
                                
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
                                $reporteWord->generated_by = Auth::user()->name;
                                $reporteWord->generated_at = now();
                                $reporteWord->save();
                                
                                // Mostrar notificación de éxito
                                Notification::make()
                                    ->success()
                                    ->title('Reporte generado exitosamente')
                                    ->body('El reporte Word ha sido generado y guardado correctamente.')
                                    ->send();
                                
                                // Redireccionar al índice
                                return redirect()->route('filament.admin.resources.reporte-words.index');
                                
                            } catch (\Exception $e) {
                                Log::error('Error en la generación del reporte: ' . $e->getMessage(), [
                                    'checklist_inspection_id' => $checklistInspectionId ?? null,
                                    'user_id' => Auth::id(),
                                    'trace' => $e->getTraceAsString()
                                ]);
                                
                                Notification::make()
                                    ->danger()
                                    ->title('Error al generar el reporte')
                                    ->body('Ocurrió un error: ' . $e->getMessage())
                                    ->send();
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Generar Reporte Word')
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
                Tables\Filters\SelectFilter::make('owner')
                    ->relationship('checklistInspection.owner', 'name')
                    ->label('Propietario'),

                Tables\Filters\SelectFilter::make('vessel')
                    ->relationship('checklistInspection.vessel', 'name')
                    ->label('Embarcación'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('cleanup')
                    ->label('Limpiar Archivos Huérfanos')
                    ->icon('heroicon-o-trash')
                    ->color('warning')
                    ->action(function () {
                        $cleaned = 0;
                        $reports = ReporteWord::all();
                        
                        foreach ($reports as $report) {
                            $fullPath = storage_path('app/private/' . $report->report_path);
                            if (!file_exists($fullPath)) {
                                $report->delete();
                                $cleaned++;
                            }
                        }
                        
                        Notification::make()
                            ->success()
                            ->title('Limpieza completada')
                            ->body("Se eliminaron {$cleaned} registros sin archivo.")
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Limpiar registros sin archivo')
                    ->modalDescription('¿Está seguro de que desea eliminar los registros de reportes cuyos archivos ya no existen?')
                    ->modalSubmitActionLabel('Limpiar'),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->url(fn (ReporteWord $record): string => url('/reporte-word/download/' . $record->id))
                    ->openUrlInNewTab()
                    ->visible(function (ReporteWord $record): bool {
                        // Only show download button if file exists
                        $fullPath = storage_path('app/private/' . $record->report_path);
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
            $section = $phpWord->addSection();
            
            // 4. Usar estilos definidos para mejor compatibilidad
            $titleStyle = ['name' => 'Arial', 'size' => 16, 'bold' => true];
            $headerStyle = ['name' => 'Arial', 'size' => 14, 'bold' => true];
            $normalStyle = ['name' => 'Arial', 'size' => 12];
            
            // Contenido del reporte con estilos apropiados
            $section->addText('INFORME DE INSPECCIÓN CHECKLIST', $titleStyle);
            $section->addTextBreak(1);
            $section->addText('========================================', $normalStyle);
            $section->addTextBreak(1);
            $section->addText('INFORMACIÓN GENERAL', $headerStyle);
            $section->addTextBreak(1);
            
            // Usar htmlspecialchars para escapar caracteres especiales
            $section->addText('Propietario: ' . htmlspecialchars($inspection->owner->name, ENT_QUOTES, 'UTF-8'), $normalStyle);
            $section->addText('Embarcación: ' . htmlspecialchars($inspection->vessel->name, ENT_QUOTES, 'UTF-8'), $normalStyle);
            $section->addText('Inspector: ' . htmlspecialchars($inspection->inspector_name, ENT_QUOTES, 'UTF-8'), $normalStyle);
            $section->addText('Fecha: ' . $inspection->inspection_start_date->format('d/m/Y'), $normalStyle);
            $section->addText('Estado: ' . htmlspecialchars($inspection->overall_status ?? 'N/A', ENT_QUOTES, 'UTF-8'), $normalStyle);
            $section->addTextBreak(1);
            
            // Partes del checklist con mejor manejo de caracteres especiales
            for ($i = 1; $i <= 6; $i++) {
                $section->addText('PARTE ' . $i, $headerStyle);
                $section->addText('--------', $normalStyle);
                $section->addTextBreak(1);
                
                $items = $inspection->{"parte_{$i}_items"} ?? [];
                
                if (empty($items)) {
                    $section->addText('Sin items', $normalStyle);
                } else {
                    foreach ($items as $index => $item) {
                        $itemText = htmlspecialchars($item['item'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                        $estadoText = htmlspecialchars($item['estado'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                        
                        $section->addText(($index + 1) . '. ' . $itemText, $normalStyle);
                        $section->addText('   Estado: ' . $estadoText, $normalStyle);
                        
                        if (!empty($item['comentarios'])) {
                            $comentarios = htmlspecialchars($item['comentarios'], ENT_QUOTES, 'UTF-8');
                            $section->addText('   Comentarios: ' . $comentarios, $normalStyle);
                        }
                        $section->addTextBreak(1);
                    }
                }
                $section->addTextBreak(1);
            }
            
            // Observaciones generales con escape de caracteres
            if (!empty($inspection->general_observations)) {
                $section->addText('OBSERVACIONES GENERALES', $headerStyle);
                $section->addTextBreak(1);
                $observations = htmlspecialchars($inspection->general_observations, ENT_QUOTES, 'UTF-8');
                $section->addText($observations, $normalStyle);
            }
            
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
}