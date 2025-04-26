<?php

namespace App\Filament\Resources\ShipyardResource\Pages;

use App\Filament\Resources\ShipyardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShipyard extends EditRecord
{
    protected static string $resource = ShipyardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
