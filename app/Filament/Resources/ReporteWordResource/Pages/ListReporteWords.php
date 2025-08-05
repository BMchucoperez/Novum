<?php

namespace App\Filament\Resources\ReporteWordResource\Pages;

use App\Filament\Resources\ReporteWordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReporteWords extends ListRecords
{
    protected static string $resource = ReporteWordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
