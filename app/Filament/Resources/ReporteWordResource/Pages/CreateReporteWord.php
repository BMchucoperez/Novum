<?php

namespace App\Filament\Resources\ReporteWordResource\Pages;

use App\Filament\Resources\ReporteWordResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\ReporteWord;
use App\Models\ChecklistInspection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;
use Illuminate\Support\Str;

class CreateReporteWord extends CreateRecord
{
    protected static string $resource = ReporteWordResource::class;
    
    // Hide default form buttons (Crear, Crear y crear otro, Cancelar)
    protected function getFormActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generar el reporte Word
        $reportPath = $this->generateWordReport($data['checklist_inspection_id']);
        
        $data['report_path'] = $reportPath;
        $data['generated_by'] = Auth::user()->name;
        $data['generated_at'] = now();
        
        return $data;
    }

    protected function generateWordReport($checklistInspectionId)
    {
        $inspection = ChecklistInspection::with(['owner', 'vessel'])->findOrFail($checklistInspectionId);
        
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(12);
        
        // Sección de título
        $section = $phpWord->addSection();
        $section->addTitle('Informe de Inspección Checklist', 1);
        
        // Información general
        $section->addText('Información General de la Inspección', ['bold' => true, 'size' => 14]);
        $section->addText("Propietario: " . $inspection->owner->name);
        $section->addText("Embarcación: " . $inspection->vessel->name);
        $section->addText("Fecha de Inicio: " . $inspection->inspection_start_date->format('d/m/Y'));
        $section->addText("Fecha de Fin: " . $inspection->inspection_end_date->format('d/m/Y'));
        $section->addText("Fecha de Convoy: " . $inspection->convoy_date->format('d/m/Y'));
        $section->addText("Inspector: " . $inspection->inspector_name);
        $section->addTextBreak(1);
        
        // Partes de la inspección
        $partes = [
            1 => 'DOCUMENTOS DE BANDEIRA E APOLICES DE SEGURO',
            2 => 'DOCUMENTOS DO SISTEMA DE GESTÃO DE BORDO',
            3 => 'CASCO Y ESTRUTURAS',
            4 => 'SISTEMAS DE CARGA E DESCARGA E DE ALARME DE NIVEL',
            5 => 'SEGURANÇA, SALVAMENTO, CONTRA INCÊNDIO E LUZES DE NAVEGAÇÃO',
            6 => 'SISTEMA DE AMARRAÇÃO'
        ];
        
        foreach ($partes as $parteNum => $parteNombre) {
            $section->addText("Parte {$parteNum}: {$parteNombre}", ['bold' => true, 'size' => 13]);
            $section->addTextBreak(1);
            
            $items = $inspection->{"parte_{$parteNum}_items"};
            
            foreach ($items as $index => $item) {
                // Agregar el ítem con su estado
                $estado = match($item['estado']) {
                    'V' => 'Vigente',
                    'A' => 'Anual',
                    'N' => 'No Aplica',
                    'R' => 'Rechazado',
                    default => $item['estado']
                };
                
                $section->addText("Ítem " . ($index + 1) . ": " . $item['item'], ['bold' => true]);
                $section->addText("Estado: " . $estado);
                
                // Para las partes 3-6, incluir imágenes si existen
                if ($parteNum >= 3 && !empty($item['archivos_adjuntos'])) {
                    foreach ($item['archivos_adjuntos'] as $archivo) {
                        $filePath = storage_path('app/private/' . $archivo);
                        if (file_exists($filePath)) {
                            // Verificar si es una imagen
                            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                            if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])) {
                                $section->addImage($filePath, ['width' => 400, 'height' => 300]);
                            }
                        }
                    }
                }
                
                // Agregar comentarios si existen
                if (!empty($item['comentarios'])) {
                    $section->addText("Comentarios: " . $item['comentarios']);
                }
                
                $section->addTextBreak(1);
            }
            
            $section->addTextBreak(1);
        }
        
        // Observaciones generales
        if (!empty($inspection->general_observations)) {
            $section->addText('Observaciones Generales', ['bold' => true, 'size' => 14]);
            $section->addText($inspection->general_observations);
        }
        
        // Guardar el documento
        $fileName = 'reporte_checklist_' . $inspection->id . '_' . time() . '.docx';
        $filePath = 'reports/' . $fileName;
        $fullPath = storage_path('app/private/' . $filePath);
        
        // Crear directorio si no existe
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }
        
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($fullPath);
        
        return $filePath;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}