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
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}