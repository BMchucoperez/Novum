<?php

namespace App\Filament\Resources\ChecklistInspectionResource\Pages;

use App\Filament\Resources\ChecklistInspectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChecklistInspection extends EditRecord
{
    protected static string $resource = ChecklistInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_pdf')
                ->label('Descargar PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn () => ChecklistInspectionResource::downloadPDF($this->record)),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Calcula el estado de cada item antes de guardar
     * Esto asegura que los estados se calculen correctamente incluso si
     * los eventos afterStateUpdated no se ejecutaron
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Procesar cada parte (1 a 6)
        for ($i = 1; $i <= 6; $i++) {
            $fieldName = "parte_{$i}_items";

            if (isset($data[$fieldName]) && is_array($data[$fieldName])) {
                foreach ($data[$fieldName] as $index => $item) {
                    // Calcular el estado según la lógica documentada
                    $estado = $this->calculateItemEstado($item);
                    $data[$fieldName][$index]['estado'] = $estado;
                }
            }
        }

        return $data;
    }

    /**
     * Calcula el estado de un item según la lógica documentada
     *
     * Lógica:
     * - Si checkbox_1 (Cumple) está marcado → 'A' (APTO)
     * - Si checkbox_2 (No Cumple) está marcado:
     *   - Si prioridad === 1 → 'N' (NO APTO - Crítico)
     *   - Si prioridad === 2 o 3 → 'O' (OBSERVADO - No crítico)
     * - Si ninguno está marcado → '' (vacío)
     */
    private function calculateItemEstado(array $item): string
    {
        $checkbox1 = $item['checkbox_1'] ?? false;
        $checkbox2 = $item['checkbox_2'] ?? false;
        $prioridad = $item['prioridad'] ?? 3;

        // Si "Cumple" está marcado
        if ($checkbox1 === true) {
            return 'A'; // APTO
        }

        // Si "No Cumple" está marcado
        if ($checkbox2 === true) {
            if ($prioridad === 1) {
                return 'N'; // NO APTO (Crítico)
            } else {
                return 'O'; // OBSERVADO (No crítico)
            }
        }

        // Si ninguno está marcado
        return '';
    }
}
