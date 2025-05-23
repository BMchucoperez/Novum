<?php

namespace App\Filament\Resources\NavigationTypeResource\Pages;

use App\Filament\Resources\NavigationTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNavigationType extends EditRecord
{
    protected static string $resource = NavigationTypeResource::class;

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
