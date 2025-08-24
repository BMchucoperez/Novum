<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VesselAssociationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunas embarcaciones para crear asociaciones de ejemplo
        $vessels = \App\Models\Vessel::take(6)->get();
        
        if ($vessels->count() >= 3) {
            // Crear asociaciones de ejemplo
            // Embarcación 1 tiene asociadas la 2 y 3
            \App\Models\VesselAssociation::create([
                'main_vessel_id' => $vessels[0]->id,
                'associated_vessel_id' => $vessels[1]->id,
            ]);
            
            \App\Models\VesselAssociation::create([
                'main_vessel_id' => $vessels[0]->id,
                'associated_vessel_id' => $vessels[2]->id,
            ]);
            
            // Embarcación 4 tiene asociada la 5
            if ($vessels->count() >= 5) {
                \App\Models\VesselAssociation::create([
                    'main_vessel_id' => $vessels[3]->id,
                    'associated_vessel_id' => $vessels[4]->id,
                ]);
            }
        }
        
        $this->command->info('Asociaciones de embarcaciones creadas exitosamente.');
    }
}
