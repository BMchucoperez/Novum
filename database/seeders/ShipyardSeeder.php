<?php

namespace Database\Seeders;

use App\Models\Shipyard;
use Illuminate\Database\Seeder;

class ShipyardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shipyards = [
            [
                'name' => 'Astillero Naval Callao',
                'location' => 'Callao, Perú',
                'contact' => '+51 987654321 / info@astilleronavalcallao.com',
            ],
            [
                'name' => 'Astillero Amazonas',
                'location' => 'Iquitos, Perú',
                'contact' => '+51 987654322 / contacto@astilleroamazonas.com',
            ],
            [
                'name' => 'Astillero Fluvial Pucallpa',
                'location' => 'Pucallpa, Perú',
                'contact' => '+51 987654323 / info@astilleropucallpa.com',
            ],
            [
                'name' => 'Astillero Marítimo Chimbote',
                'location' => 'Chimbote, Perú',
                'contact' => '+51 987654324 / contacto@astillerochimbote.com',
            ],
            [
                'name' => 'Astillero Naval Ilo',
                'location' => 'Ilo, Perú',
                'contact' => '+51 987654325 / info@astilleroilo.com',
            ],
            [
                'name' => 'Astillero Paita',
                'location' => 'Paita, Perú',
                'contact' => '+51 987654326 / contacto@astilleropaita.com',
            ],
            [
                'name' => 'Construcciones Navales del Norte',
                'location' => 'Talara, Perú',
                'contact' => '+51 987654327 / info@navalesdelnorte.com',
            ],
            [
                'name' => 'Astillero Río Itaya',
                'location' => 'Iquitos, Perú',
                'contact' => '+51 987654328 / contacto@astilleroitaya.com',
            ],
        ];

        foreach ($shipyards as $shipyard) {
            Shipyard::create($shipyard);
        }
    }
}
