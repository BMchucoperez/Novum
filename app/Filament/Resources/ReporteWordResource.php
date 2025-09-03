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
                            
                            // Generar el reporte Word
                            $reportPath = self::generateWordReport($checklistInspectionId);
                            
                            // Verificar si hubo un error en la generación del reporte
                            if ($reportPath === null) {
                                return;
                            }
                            
                            // Obtener la información de la inspección de checklist
                            $inspection = ChecklistInspection::with(['owner', 'vessel'])->findOrFail($checklistInspectionId);
                            
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
                                ->title('Reporte generado')
                                ->body('El reporte Word ha sido generado exitosamente.')
                                ->send();
                            
                            // Redireccionar al índice
                            return redirect()->route('filament.admin.resources.reporte-words.index');
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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('owner')
                    ->relationship('checklistInspection.owner', 'name')
                    ->label('Propietario'),

                Tables\Filters\SelectFilter::make('vessel')
                    ->relationship('checklistInspection.vessel', 'name')
                    ->label('Embarcación'),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->url(fn (ReporteWord $record): string => route('reporte-word.download', $record->id))
                    ->openUrlInNewTab(),
                
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
        $inspection = ChecklistInspection::with(['owner', 'vessel'])->findOrFail($checklistInspectionId);
        
        // Create a new PhpWord instance with absolute minimal settings
        $phpWord = new PhpWord();
        
        // Add a simple section
        $section = $phpWord->addSection();
        
        // Add basic information as plain text only
        $section->addText('INFORME DE INSPECCIÓN CHECKLIST');
        $section->addText('-----------------------------------------');
        $section->addText('');
        
        // Basic information
        $section->addText('Propietario: ' . $inspection->owner->name);
        $section->addText('Embarcación: ' . $inspection->vessel->name);
        $section->addText('Fecha de Inicio: ' . $inspection->inspection_start_date->format('d/m/Y'));
        $section->addText('Fecha de Fin: ' . $inspection->inspection_end_date->format('d/m/Y'));
        $section->addText('Fecha de Convoy: ' . $inspection->convoy_date->format('d/m/Y'));
        $section->addText('Inspector: ' . $inspection->inspector_name);
        $section->addText('');
        
        // Parts summary with minimal formatting
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
            $section->addText('');
            $section->addText('PARTE ' . $parteNum . ': ' . $parteNombre);
            $section->addText('-----------------------------------------');
            
            $items = $inspection->{"parte_{$parteNum}_items"};
            
            if (empty($items)) {
                $section->addText('No hay items para esta parte.');
                continue;
            }
            
            // List items simply
            foreach ($items as $index => $item) {
                $estado = match($item['estado']) {
                    'V' => 'Vigente',
                    'A' => 'Anual',
                    'N' => 'No Aplica',
                    'R' => 'Rechazado',
                    default => $item['estado']
                };
                
                $section->addText('Item ' . ($index + 1) . ': ' . $item['item']);
                $section->addText('Estado: ' . $estado);
                
                if (!empty($item['comentarios'])) {
                    $section->addText('Comentarios: ' . $item['comentarios']);
                }
                
                $section->addText('');
            }
        }
        
        // No images, no complex formatting, no tables
        
        // Guardar el documento con el mínimo de elementos
        $fileName = 'reporte_simple_' . $inspection->id . '_' . time() . '.docx';
        $filePath = 'reports/' . $fileName;
        $fullPath = storage_path('app/private/' . $filePath);
        
        // Crear directorio si no existe
        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
            \Log::info('Creando directorio: ' . $directory);
            if (!mkdir($directory, 0755, true)) {
                \Log::error('No se pudo crear el directorio: ' . $directory);
                throw new \Exception('No se pudo crear el directorio para el reporte.');
            }
        }
        
        // Simple save with minimal options
        try {
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($fullPath);
            
            if (!file_exists($fullPath)) {
                throw new \Exception('El archivo no se ha creado correctamente.');
            }
            
            return $filePath;
        } catch (\Exception $e) {
            \Log::error('Error al generar el reporte Word simple: ' . $e->getMessage());
            throw $e;
        }
    }
}