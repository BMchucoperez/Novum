<?php

namespace App\Filament\Resources\ChecklistInspectionResource\Pages;

use App\Filament\Resources\ChecklistInspectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewChecklistInspection extends ViewRecord
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
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}