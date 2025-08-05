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
        
        // Convertir a string y limpiar caracteres problemáticos
        $text = (string) $text;
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        $text = trim($text);
        
        return $text;
    }

    private static function processModuleData($section, $titulo, $record): void
    {
        switch ($titulo) {
            case 'Estructura y Maquinaria':
                for ($i = 1; $i <= 13; $i++) {
                    $items = $record->{"parte_{$i}_items"} ?? [];
                    if (!empty($items) && is_array($items)) {
                        $section->addText("Parte $i:", ['bold' => true]);
                        self::addTextList($section, $items);
                        $section->addTextBreak(1);
                    }
                }
                break;
                
            case 'Certificados Estatutarios':
                for ($i = 1; $i <= 6; $i++) {
                    $items = $record->{"parte_{$i}_items"} ?? [];
                    if (!empty($items) && is_array($items)) {
                        $section->addText("Parte $i:", ['bold' => true]);
                        self::addTextList($section, $items);
                        $section->addTextBreak(1);
                    }
                }
                break;
                
            case 'Documentos de Gestion a Bordo':
                for ($i = 1; $i <= 3; $i++) {
                    $items = $record->{"parte_{$i}_items"} ?? [];
                    if (!empty($items) && is_array($items)) {
                        $section->addText("Parte $i:", ['bold' => true]);
                        self::addTextList($section, $items);
                        $section->addTextBreak(1);
                    }
                }
                break;
                
            case 'Tripulacion':
                $tripulantes = $record->tripulantes ?? [];
                if (!empty($tripulantes) && is_array($tripulantes)) {
                    self::addCrewList($section, $tripulantes);
                } else {
                    $section->addText('No se registraron tripulantes.');
                }
                break;
        }
        
        // Agregar observaciones generales si existen
        if (!empty($record->general_observations)) {
            $section->addText('Observaciones Generales:', ['bold' => true]);
            $section->addText(self::cleanText($record->general_observations));
            $section->addTextBreak(1);
        }
    }

    private static function addTextList($section, $items): void
    {
        foreach ($items as $item) {
            if (is_array($item)) {
                $itemText = self::cleanText($item['item'] ?? '');
                $estado = self::cleanText($item['estado'] ?? '');
                $comentarios = self::cleanText($item['comentarios'] ?? '');
                
                $section->addText("• $itemText - Estado: $estado");
                if (!empty($comentarios)) {
                    $section->addText("  Comentarios: $comentarios");
                }
            }
        }
    }

    private static function addCrewList($section, $tripulantes): void
    {
        foreach ($tripulantes as $t) {
            if (is_array($t)) {
                $cargo = self::cleanText($t['cargo'] ?? '');
                $nombre = self::cleanText($t['nombre'] ?? '');
                $matricula = self::cleanText($t['matricula'] ?? '');
                $comentarios = self::cleanText($t['comentarios'] ?? '');
                
                $section->addText("• $cargo: $nombre (Matricula: $matricula)");
                if (!empty($comentarios)) {
                    $section->addText("  Comentarios: $comentarios");
                }
            }
        }
    }

    private static function addCompleteModuleData($section, $titulo, $record): void
    {
        switch ($titulo) {
            case 'Estructura y Maquinaria':
                for ($i = 1; $i <= 13; $i++) {
                    $items = $record->{"parte_{$i}_items"} ?? [];
                    if (!empty($items) && is_array($items)) {
                        $section->addText("Parte $i:");
                        
                        foreach ($items as $item) {
                            if (is_array($item)) {
                                $itemText = self::cleanText($item['item'] ?? '');
                                $estado = self::cleanText($item['estado'] ?? '');
                                $comentarios = self::cleanText($item['comentarios'] ?? '');
                                
                                $line = "• $itemText - Estado: $estado";
                                if (!empty($comentarios)) {
                                    $line .= " - Comentarios: $comentarios";
                                }
                                $section->addText($line);
                            }
                        }
                        $section->addText(''); // Línea vacía
                    }
                }
                break;
                
            case 'Certificados Estatutarios':
                for ($i = 1; $i <= 6; $i++) {
                    $items = $record->{"parte_{$i}_items"} ?? [];
                    if (!empty($items) && is_array($items)) {
                        $section->addText("Parte $i:");
                        
                        foreach ($items as $item) {
                            if (is_array($item)) {
                                $itemText = self::cleanText($item['item'] ?? '');
                                $estado = self::cleanText($item['estado'] ?? '');
                                $comentarios = self::cleanText($item['comentarios'] ?? '');
                                
                                $line = "• $itemText - Estado: $estado";
                                if (!empty($comentarios)) {
                                    $line .= " - Comentarios: $comentarios";
                                }
                                $section->addText($line);
                            }
                        }
                        $section->addText(''); // Línea vacía
                    }
                }
                break;
                
            case 'Documentos de Gestion a Bordo':
                for ($i = 1; $i <= 3; $i++) {
                    $items = $record->{"parte_{$i}_items"} ?? [];
                    if (!empty($items) && is_array($items)) {
                        $section->addText("Parte $i:");
                        
                        foreach ($items as $item) {
                            if (is_array($item)) {
                                $itemText = self::cleanText($item['item'] ?? '');
                                $estado = self::cleanText($item['estado'] ?? '');
                                $comentarios = self::cleanText($item['comentarios'] ?? '');
                                
                                $line = "• $itemText - Estado: $estado";
                                if (!empty($comentarios)) {
                                    $line .= " - Comentarios: $comentarios";
                                }
                                $section->addText($line);
                            }
                        }
                        $section->addText(''); // Línea vacía
                    }
                }
                break;
                
            case 'Tripulacion':
                $tripulantes = $record->tripulantes ?? [];
                if (!empty($tripulantes) && is_array($tripulantes)) {
                    $section->addText("Lista de Tripulacion:");
                    
                    foreach ($tripulantes as $t) {
                        if (is_array($t)) {
                            $cargo = self::cleanText($t['cargo'] ?? '');
                            $nombre = self::cleanText($t['nombre'] ?? '');
                            $matricula = self::cleanText($t['matricula'] ?? '');
                            $comentarios = self::cleanText($t['comentarios'] ?? '');
                            
                            $line = "• $cargo: $nombre (Matricula: $matricula)";
                            if (!empty($comentarios)) {
                                $line .= " - Comentarios: $comentarios";
                            }
                            $section->addText($line);
                        }
                    }
                } else {
                    $section->addText('No se registraron tripulantes.');
                }
                break;
        }
        
        // Agregar observaciones generales si existen
        if (!empty($record->general_observations)) {
            $section->addText('Observaciones Generales:');
            $section->addText(self::cleanText($record->general_observations));
        }
        
        $section->addText(''); // Línea vacía al final
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
                        return Storage::disk('public')->download($record->file_path);
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
                        $fileName = 'informe_consolidado_' . now()->format('Ymd_His') . '.docx';
                        $directory = storage_path('app/public/reports');
                        if (!file_exists($directory)) {
                            mkdir($directory, 0755, true);
                        }
                        $filePath = 'reports/' . $fileName;

                        try {
                            // Crear documento Word mínimo
                            $phpWord = new \PhpOffice\PhpWord\PhpWord();
                            $section = $phpWord->addSection();
                            
                            // Solo texto básico
                            $section->addText('INFORME CONSOLIDADO DE INSPECCION');
                            $section->addText('Fecha: ' . \Carbon\Carbon::parse($data['inspection_date'])->format('d/m/Y'));
                            $section->addText('Inspector: ' . $data['inspector_name']);
                            $section->addText('');

                            $models = [
                                'Estructura y Maquinaria' => StructureAndMachinery::class,
                                'Certificados Estatutarios' => StatutoryCertificate::class,
                                'Documentos de Gestion a Bordo' => OnboardManagementDocument::class,
                                'Tripulacion' => CrewMember::class,
                            ];

                            $found = false;

                            foreach ($models as $titulo => $modelClass) {
                                try {
                                    $query = $modelClass::query()
                                        ->where('owner_id', $data['owner_id'])
                                        ->where('vessel_id', $data['vessel_id'])
                                        ->whereDate('inspection_date', $data['inspection_date']);

                                    if (!empty($data['vessel2_id'])) $query->where('vessel_2_id', $data['vessel2_id']);
                                    if (!empty($data['vessel3_id'])) $query->where('vessel_3_id', $data['vessel3_id']);

                                    $records = $query->get();
                                    if ($records->isNotEmpty()) {
                                        $found = true;
                                        
                                        $section->addText($titulo);
                                        $section->addText('');
                                        
                                        foreach ($records as $record) {
                                            // Procesar módulos de forma segura
                                            if ($titulo === 'Estructura y Maquinaria') {
                                                for ($i = 1; $i <= 13; $i++) {
                                                    $items = $record->{"parte_{$i}_items"} ?? [];
                                                    if (!empty($items) && is_array($items)) {
                                                        $section->addText("Parte $i:");
                                                        foreach ($items as $item) {
                                                            if (is_array($item)) {
                                                                $itemText = self::cleanText($item['item'] ?? '');
                                                                $estado = self::cleanText($item['estado'] ?? '');
                                                                $comentarios = self::cleanText($item['comentarios'] ?? '');
                                                                
                                                                $line = "- $itemText ($estado)";
                                                                if (!empty($comentarios)) {
                                                                    $line .= " - $comentarios";
                                                                }
                                                                $section->addText($line);
                                                            }
                                                        }
                                                        $section->addText('');
                                                    }
                                                }
                                            } elseif ($titulo === 'Certificados Estatutarios') {
                                                for ($i = 1; $i <= 6; $i++) {
                                                    $items = $record->{"parte_{$i}_items"} ?? [];
                                                    if (!empty($items) && is_array($items)) {
                                                        $section->addText("Parte $i:");
                                                        foreach ($items as $item) {
                                                            if (is_array($item)) {
                                                                $itemText = self::cleanText($item['item'] ?? '');
                                                                $estado = self::cleanText($item['estado'] ?? '');
                                                                $comentarios = self::cleanText($item['comentarios'] ?? '');
                                                                
                                                                $line = "- $itemText ($estado)";
                                                                if (!empty($comentarios)) {
                                                                    $line .= " - $comentarios";
                                                                }
                                                                $section->addText($line);
                                                            }
                                                        }
                                                        $section->addText('');
                                                    }
                                                }
                                            } else {
                                                // Solo mostrar que hay datos para los otros módulos por ahora
                                                $section->addText('Datos encontrados para ' . $titulo);
                                            }
                                            
                                            // Agregar observaciones generales de forma segura
                                            if (!empty($record->general_observations)) {
                                                $section->addText('Observaciones Generales:');
                                                $section->addText(self::cleanText($record->general_observations));
                                            }
                                            $section->addText('');
                                        }
                                    }
                                } catch (\Throwable $e) {
                                    $section->addText('Error en ' . $titulo);
                                    continue;
                                }
                            }

                            if (!$found) {
                                $section->addText('No se encontraron registros.');
                            }

                            // Guardar
                            $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                            $writer->save(storage_path('app/public/' . $filePath));
                            
                        } catch (\Throwable $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                            return;
                        }

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
                            ->title('Informe generado y guardado correctamente.')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ReporteWordResource\Pages\ListReporteWords::route('/'),
        ];
    }
}
