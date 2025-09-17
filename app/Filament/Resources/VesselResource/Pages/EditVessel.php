<?php

namespace App\Filament\Resources\VesselResource\Pages;

use App\Filament\Resources\VesselResource;
use App\Models\VesselAssociation;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVessel extends EditRecord
{
    protected static string $resource = VesselResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        $this->handleVesselAssociations();
    }

    protected function handleVesselAssociations(): void
    {
        $associatedVessels = $this->data['associated_vessels'] ?? [];
        
        // Eliminar asociaciones existentes
        VesselAssociation::where('main_vessel_id', $this->record->id)->delete();
        
        // Crear nuevas asociaciones
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
