<?php

namespace Database\Seeders;

use App\Models\Vessel;
use App\Models\ServiceType;
use App\Models\NavigationType;
use App\Models\Shipyard;
use App\Models\Owner;
use Illuminate\Database\Seeder;

class VesselSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener IDs de los modelos relacionados
        $serviceTypeIds = ServiceType::pluck('id')->toArray();
        $navigationTypeIds = NavigationType::pluck('id')->toArray();
        $shipyardIds = Shipyard::pluck('id')->toArray();
        $ownerIds = Owner::pluck('id')->toArray();

        // Nombres de embarcaciones
        $vesselNames = [
            'AMAZONAS I', 'RÍO UCAYALI', 'MARAÑÓN EXPRESS', 'CALLAO STAR', 
            'IQUITOS TRADER', 'PUCALLPA EXPLORER', 'HUALLAGA QUEEN', 'NAPO VOYAGER',
            'PACHITEA PRINCESS', 'NANAY NAVIGATOR', 'TIGRE TRANSPORTER', 'PASTAZA PIONEER',
            'MADRE DE DIOS', 'URUBAMBA UNITY', 'TAMBOPATA TRAVELER', 'APURÍMAC ADVENTURE',
            'MANTARO MAJESTY', 'YAVARÍ YACHT', 'PUTUMAYO PRIDE', 'MORONA MASTER'
        ];

        // Puertos de registro
        $registryPorts = [
            'Iquitos', 'Pucallpa', 'Yurimaguas', 'Callao', 'Chimbote', 'Ilo', 'Paita', 'Talara'
        ];

        // Banderas de registro
        $flagRegistries = [
            'Peruana', 'Brasileña', 'Colombiana', 'Ecuatoriana', 'Boliviana'
        ];

        // Crear 20 embarcaciones con datos aleatorios
        for ($i = 0; $i < 20; $i++) {
            Vessel::create([
                'name' => $vesselNames[$i],
                'service_type_id' => $serviceTypeIds[array_rand($serviceTypeIds)],
                'navigation_type_id' => $navigationTypeIds[array_rand($navigationTypeIds)],
                'flag_registry' => $flagRegistries[array_rand($flagRegistries)],
                'port_registry' => $registryPorts[array_rand($registryPorts)],
                'construction_year' => rand(1990, 2023),
                'shipyard_id' => $shipyardIds[array_rand($shipyardIds)],
                'length' => rand(1000, 10000) / 100, // Entre 10 y 100 metros
                'beam' => rand(200, 2000) / 100,     // Entre 2 y 20 metros
                'depth' => rand(100, 1000) / 100,    // Entre 1 y 10 metros
                'gross_tonnage' => rand(5000, 50000) / 100, // Entre 50 y 500 toneladas
                'registration_number' => 'PA-' . rand(10000, 99999) . '-' . chr(rand(65, 90)) . chr(rand(65, 90)),
                'owner_id' => $ownerIds[array_rand($ownerIds)],
            ]);
        }
    }
}
