<?php

namespace App\Filament\Resources\InspectionScheduleResource\Pages;

use App\Filament\Resources\InspectionScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInspectionSchedules extends ListRecords
{
    protected static string $resource = InspectionScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
