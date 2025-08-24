<?php

namespace App\Filament\Resources\VesselResource\Pages;

use App\Filament\Resources\VesselResource;
use App\Models\VesselAssociation;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVessel extends CreateRecord
{
    protected static string $resource = VesselResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $this->handleVesselAssociations();
    }

    protected function handleVesselAssociations(): void
    {
        $associatedVessels = $this->data['associated_vessels'] ?? [];
        
        if (!empty($associatedVessels)) {
            foreach ($associatedVessels as $associatedVesselId) {
                VesselAssociation::create([
                    'main_vessel_id' => $this->record->id,
                    'associated_vessel_id' => $associatedVesselId,
                ]);
            }
        }
    }
}
