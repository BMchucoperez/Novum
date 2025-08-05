<?php

namespace App\Filament\Resources\ReporteWordResource\Pages;

use App\Filament\Resources\ReporteWordResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReporteWord extends EditRecord
{
    protected static string $resource = ReporteWordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
