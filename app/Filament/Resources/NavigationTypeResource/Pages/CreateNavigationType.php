<?php

namespace App\Filament\Resources\NavigationTypeResource\Pages;

use App\Filament\Resources\NavigationTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNavigationType extends CreateRecord
{
    protected static string $resource = NavigationTypeResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
