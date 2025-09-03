<?php

namespace App\Filament\Resources\ReporteWordResource\Pages;

use App\Filament\Resources\ReporteWordResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewReporteWord extends ViewRecord
{
    protected static string $resource = ReporteWordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}