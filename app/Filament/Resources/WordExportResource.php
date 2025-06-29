<?php

namespace App\Filament\Resources;

use App\Models\StructureAndMachinery;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;

class WordExportResource extends Resource
{
    protected static ?string $model = StructureAndMachinery::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Exportaciones';
    protected static ?string $navigationLabel = 'Exportar a Word';
    protected static ?string $modelLabel = 'Exportar a Word';
    protected static ?string $pluralModelLabel = 'Exportar a Word';
    protected static ?int $navigationSort = 99;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('owner.name')->label('Propietario'),
                Tables\Columns\TextColumn::make('vessel.name')->label('Embarcación 1'),
                Tables\Columns\TextColumn::make('vessel2.name')->label('Embarcación 2')->placeholder('—'),
                Tables\Columns\TextColumn::make('vessel3.name')->label('Embarcación 3')->placeholder('—'),
                Tables\Columns\TextColumn::make('inspection_date')->label('Fecha de Inspección')->date('d/m/Y'),
                Tables\Columns\TextColumn::make('inspector_name')->label('Inspector'),
            ])
            ->actions([
                Action::make('exportWord')
                    ->label('Exportar a Word')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($record) {
                        $phpWord = new PhpWord();
                        $section = $phpWord->addSection();
                        // Encabezado general
                        $section->addTitle('Inspección de Estructura y Maquinaria', 1);
                        $section->addText('Propietario: ' . ($record->owner->name ?? ''));
                        $section->addText('Embarcación 1: ' . ($record->vessel->name ?? ''));
                        $section->addText('Embarcación 2: ' . ($record->vessel2->name ?? ''));
                        $section->addText('Embarcación 3: ' . ($record->vessel3->name ?? ''));
                        $section->addText('Fecha de Inspección: ' . ($record->inspection_date ? $record->inspection_date->format('d/m/Y') : ''));
                        $section->addText('Inspector: ' . ($record->inspector_name ?? ''));
                        $section->addTextBreak(1);

                        // Recorrer los 13 módulos/partes
                        for ($i = 1; $i <= 13; $i++) {
                            $partKey = 'parte_' . $i . '_items';
                            $partItems = $record->{$partKey} ?? [];
                            if (empty($partItems)) continue;
                            $section->addTitle('Parte ' . $i, 2);
                            foreach ($partItems as $idx => $item) {
                                $section->addText('Ítem: ' . ($item['nombre_item'] ?? ''));
                                $section->addText('Estado: ' . ($item['estado'] ?? ''));
                                $section->addText('Comentarios: ' . ($item['comentarios'] ?? ''));
                                $section->addText('Observaciones: ' . ($item['observaciones'] ?? ''));
                                // Imágenes (si existen)
                                if (!empty($item['imagenes']) && is_array($item['imagenes'])) {
                                    foreach ($item['imagenes'] as $imgPath) {
                                        $fullPath = public_path('storage/' . ltrim($imgPath, '/'));
                                        if (file_exists($fullPath)) {
                                            $section->addImage($fullPath, ['width' => 200, 'height' => 150]);
                                        }
                                    }
                                }
                                $section->addTextBreak(1);
                            }
                            $section->addTextBreak(1);
                        }
                        $fileName = 'inspeccion_' . ($record->id ?? 'export') . '.docx';
                        return response()->streamDownload(function () use ($phpWord) {
                            $writer = IOFactory::createWriter($phpWord, 'Word2007');
                            $writer->save('php://output');
                        }, $fileName, [
                            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        ]);
                    }),
            ])
            ->bulkActions([
                // ...existing code...
            ])
            ->headerActions([
                Action::make('generateReport')
                    ->label('Generar informe')
                    ->icon('heroicon-o-document-text')
                    ->form([
                        Select::make('owner_id')
                            ->label('Propietario')
                            ->relationship('owner', 'name')
                            ->required(),
                        Select::make('vessel_id')
                            ->label('Embarcación 1')
                            ->relationship('vessel', 'name')
                            ->required(),
                        Select::make('vessel2_id')
                            ->label('Embarcación 2')
                            ->relationship('vessel2', 'name'),
                        Select::make('vessel3_id')
                            ->label('Embarcación 3')
                            ->relationship('vessel3', 'name'),
                        DatePicker::make('inspection_date')
                            ->label('Fecha de Inspección')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $query = StructureAndMachinery::query()
                            ->where('owner_id', $data['owner_id'])
                            ->where('vessel_id', $data['vessel_id'])
                            ->whereDate('inspection_date', $data['inspection_date']);
                        if (!empty($data['vessel2_id'])) {
                            $query->where('vessel2_id', $data['vessel2_id']);
                        }
                        if (!empty($data['vessel3_id'])) {
                            $query->where('vessel3_id', $data['vessel3_id']);
                        }
                        $records = $query->get();
                        if ($records->isEmpty()) {
                            \Filament\Notifications\Notification::make()
                                ->title('No se encontraron inspecciones')
                                ->danger()
                                ->send();
                            return null;
                        }
                        $phpWord = new PhpWord();
                        $section = $phpWord->addSection();
                        $section->addTitle('Informe Unificado de Inspección', 1);
                        $section->addText('Propietario: ' . ($records[0]->owner->name ?? ''));
                        $section->addText('Embarcación 1: ' . ($records[0]->vessel->name ?? ''));
                        $section->addText('Embarcación 2: ' . ($records[0]->vessel2->name ?? ''));
                        $section->addText('Embarcación 3: ' . ($records[0]->vessel3->name ?? ''));
                        $section->addText('Fecha de Inspección: ' . ($records[0]->inspection_date ? $records[0]->inspection_date->format('d/m/Y') : ''));
                        $section->addTextBreak(1);
                        foreach ($records as $record) {
                            $section->addTitle('Inspección ID: ' . $record->id, 2);
                            for ($i = 1; $i <= 13; $i++) {
                                $partKey = 'parte_' . $i . '_items';
                                $partItems = $record->{$partKey} ?? [];
                                if (empty($partItems)) continue;
                                $section->addTitle('Parte ' . $i, 3);
                                foreach ($partItems as $idx => $item) {
                                    $section->addText('Ítem: ' . ($item['nombre_item'] ?? ''));
                                    $section->addText('Estado: ' . ($item['estado'] ?? ''));
                                    $section->addText('Comentarios: ' . ($item['comentarios'] ?? ''));
                                    $section->addText('Observaciones: ' . ($item['observaciones'] ?? ''));
                                    if (!empty($item['imagenes']) && is_array($item['imagenes'])) {
                                        foreach ($item['imagenes'] as $imgPath) {
                                            $fullPath = public_path('storage/' . ltrim($imgPath, '/'));
                                            if (file_exists($fullPath)) {
                                                $section->addImage($fullPath, ['width' => 200, 'height' => 150]);
                                            }
                                        }
                                    }
                                    $section->addTextBreak(1);
                                }
                                $section->addTextBreak(1);
                            }
                        }
                        $fileName = 'informe_unificado_' . now()->format('Ymd_His') . '.docx';
                        return response()->streamDownload(function () use ($phpWord) {
                            $writer = IOFactory::createWriter($phpWord, 'Word2007');
                            $writer->save('php://output');
                        }, $fileName, [
                            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        ]);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\WordExportResource\Pages\ListWordExports::route('/'),
        ];
    }
}
