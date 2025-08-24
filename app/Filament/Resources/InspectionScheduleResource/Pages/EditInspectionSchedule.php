<?php

namespace App\Filament\Resources\InspectionScheduleResource\Pages;

use App\Filament\Resources\InspectionScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInspectionSchedule extends EditRecord
{
    protected static string $resource = InspectionScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
