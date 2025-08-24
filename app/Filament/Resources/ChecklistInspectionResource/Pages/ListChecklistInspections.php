<?php

namespace App\Filament\Resources\ChecklistInspectionResource\Pages;

use App\Filament\Resources\ChecklistInspectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChecklistInspections extends ListRecords
{
    protected static string $resource = ChecklistInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
