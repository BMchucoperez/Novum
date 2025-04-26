<?php

namespace App\Filament\Resources\ShipyardResource\Pages;

use App\Filament\Resources\ShipyardResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateShipyard extends CreateRecord
{
    protected static string $resource = ShipyardResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
