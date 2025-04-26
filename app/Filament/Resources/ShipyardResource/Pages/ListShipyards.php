<?php

namespace App\Filament\Resources\ShipyardResource\Pages;

use App\Filament\Resources\ShipyardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShipyards extends ListRecords
{
    protected static string $resource = ShipyardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
