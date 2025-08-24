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
            Actions\DeleteAction::make(),
        ];
    }
}
