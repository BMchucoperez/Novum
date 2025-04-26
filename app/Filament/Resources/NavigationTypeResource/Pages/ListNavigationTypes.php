<?php

namespace App\Filament\Resources\NavigationTypeResource\Pages;

use App\Filament\Resources\NavigationTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNavigationTypes extends ListRecords
{
    protected static string $resource = NavigationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
