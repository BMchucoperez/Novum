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
                                \Log::error('Error en la generación del reporte: ' . $e->getMessage(), [
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
                    ->url(fn (ReporteWord $record): string => route('reporte-word.download', $record->id))
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
            
            if (!$inspection) {
                \Log::error('Inspección no encontrada: ' . $checklistInspectionId);
                Notification::make()
                    ->danger()
                    ->title('Error')
                    ->body('La inspección seleccionada no existe.')
                    ->send();
                return null;
            }
            
            // Verificar que las relaciones existen
            if (!$inspection->owner || !$inspection->vessel) {
                \Log::error('Faltan datos relacionados para la inspección: ' . $checklistInspectionId);
                Notification::make()
                    ->danger()
                    ->title('Error')
                    ->body('Faltan datos del propietario o la embarcación.')
                    ->send();
                return null;
            }
            
            // Create a new PhpWord instance
            $phpWord = new PhpWord();
            
            // Add a section
            $section = $phpWord->addSection();
            
            // Define styles
            $phpWord->addFontStyle('titleStyle', ['bold' => true, 'size' => 16]);
            $phpWord->addFontStyle('headerStyle', ['bold' => true, 'size' => 12]);
            $phpWord->addFontStyle('normalStyle', ['size' => 10]);
            
            // Add title
            $section->addText('INFORME DE INSPECCIÓN CHECKLIST', 'titleStyle');
            $section->addTextBreak();
            
            // Basic information
            $section->addText('INFORMACIÓN GENERAL', 'headerStyle');
            $section->addText('-----------------------------------------');
            $section->addText('Propietario: ' . $inspection->owner->name, 'normalStyle');
            $section->addText('Embarcación: ' . $inspection->vessel->name, 'normalStyle');
            $section->addText('Fecha de Inicio: ' . $inspection->inspection_start_date->format('d/m/Y'), 'normalStyle');
            $section->addText('Fecha de Fin: ' . $inspection->inspection_end_date->format('d/m/Y'), 'normalStyle');
            $section->addText('Fecha de Convoy: ' . $inspection->convoy_date->format('d/m/Y'), 'normalStyle');
            $section->addText('Inspector: ' . $inspection->inspector_name, 'normalStyle');
            $section->addText('Estado General: ' . ($inspection->overall_status ?? 'No definido'), 'normalStyle');
            $section->addTextBreak();
            
            // Parts summary
            $partes = [
                1 => 'DOCUMENTOS DE BANDEIRA E APOLICES DE SEGURO',
                2 => 'DOCUMENTOS DO SISTEMA DE GESTÃO DE BORDO',
                3 => 'CASCO Y ESTRUTURAS',
                4 => 'SISTEMAS DE CARGA E DESCARGA E DE ALARME DE NIVEL',
                5 => 'SEGURANÇA, SALVAMENTO, CONTRA INCÊNDIO E LUZES DE NAVEGAÇÃO',
                6 => 'SISTEMA DE AMARRAÇÃO'
            ];
            
            foreach ($partes as $parteNum => $parteNombre) {
                // Add part title
                $section->addTextBreak();
                $section->addText('PARTE ' . $parteNum . ': ' . $parteNombre, 'headerStyle');
                $section->addText('-----------------------------------------');
                
                $items = $inspection->{"parte_{$parteNum}_items"};
                
                if (empty($items)) {
                    $section->addText('No hay items para esta parte.', 'normalStyle');
                    continue;
                }
                
                // List items
                foreach ($items as $index => $item) {
                    $estado = match($item['estado'] ?? null) {
                        'V' => 'Vigente',
                        'A' => 'Anual',
                        'N' => 'No Aplica',
                        'R' => 'Rechazado',
                        default => $item['estado'] ?? 'Sin estado'
                    };
                    
                    $section->addText('Item ' . ($index + 1) . ': ' . ($item['item'] ?? 'Item sin descripción'), 'normalStyle');
                    $section->addText('Estado: ' . $estado, 'normalStyle');
                    
                    if (!empty($item['comentarios'])) {
                        $section->addText('Comentarios: ' . $item['comentarios'], 'normalStyle');
                    }
                    
                    $section->addTextBreak();
                }
            }
            
            // Add general observations if available
            if (!empty($inspection->general_observations)) {
                $section->addTextBreak();
                $section->addText('OBSERVACIONES GENERALES', 'headerStyle');
                $section->addText('-----------------------------------------');
                $section->addText($inspection->general_observations, 'normalStyle');
            }
            
            // Generate unique filename
            $fileName = 'reporte_checklist_' . $inspection->id . '_' . time() . '.docx';
            $filePath = 'reports/' . $fileName;
            $fullPath = storage_path('app/private/' . $filePath);
            
            // Create directory if it doesn't exist
            $directory = dirname($fullPath);
            if (!file_exists($directory)) {
                \Log::info('Creando directorio: ' . $directory);
                if (!mkdir($directory, 0755, true)) {
                    \Log::error('No se pudo crear el directorio: ' . $directory);
                    Notification::make()
                        ->danger()
                        ->title('Error')
                        ->body('No se pudo crear el directorio para el reporte.')
                        ->send();
                    return null;
                }
            }
            
            // Save the document
            try {
                $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
                $objWriter->save($fullPath);
            } catch (\Exception $writerException) {
                \Log::error('Error with Word2007 writer: ' . $writerException->getMessage());
                
                // Try with a simpler approach
                $simplePhpWord = new PhpWord();
                $simpleSection = $simplePhpWord->addSection();
                $simpleSection->addText('INFORME DE INSPECCIÓN CHECKLIST');
                $simpleSection->addText('');
                $simpleSection->addText('Propietario: ' . $inspection->owner->name);
                $simpleSection->addText('Embarcación: ' . $inspection->vessel->name);
                $simpleSection->addText('Fecha de Inspección: ' . $inspection->inspection_start_date->format('d/m/Y'));
                $simpleSection->addText('Inspector: ' . $inspection->inspector_name);
                
                $simpleWriter = IOFactory::createWriter($simplePhpWord, 'Word2007');
                $simpleWriter->save($fullPath);
            }
            
            // Verify file was created
            if (!file_exists($fullPath)) {
                \Log::error('El archivo no se creó: ' . $fullPath);
                Notification::make()
                    ->danger()
                    ->title('Error')
                    ->body('El archivo no se creó correctamente.')
                    ->send();
                return null;
            }
            
            // Verify file size
            $fileSize = filesize($fullPath);
            if ($fileSize === false || $fileSize < 1000) {
                \Log::error('El archivo creado es muy pequeño o está corrupto: ' . $fullPath . ' (' . $fileSize . ' bytes)');
                Notification::make()
                    ->danger()
                    ->title('Error')
                    ->body('El archivo generado parece estar corrupto.')
                    ->send();
                return null;
            }
            
            \Log::info('Reporte Word generado exitosamente: ' . $fullPath . ' (' . $fileSize . ' bytes)');
            return $filePath;
            
        } catch (\Exception $e) {
            \Log::error('Error al generar el reporte Word: ' . $e->getMessage(), [
                'checklist_inspection_id' => $checklistInspectionId,
                'trace' => $e->getTraceAsString()
            ]);
            
            Notification::make()
                ->danger()
                ->title('Error al generar el reporte')
                ->body('Ocurrió un error técnico: ' . $e->getMessage())
                ->send();
            
            return null;
        }
    }
}