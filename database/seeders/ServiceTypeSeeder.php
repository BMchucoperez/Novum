<?php

namespace Database\Seeders;

use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class ServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $serviceTypes = [
            [
                'name' => 'Empujador',
                'code' => 'EMP',
                'description' => 'Embarcación diseñada para empujar barcazas o convoyes en ríos y canales.',
            ],
            [
                'name' => 'Remolcador',
                'code' => 'REM',
                'description' => 'Embarcación potente diseñada para remolcar o empujar otras embarcaciones en puertos, mar abierto o vías navegables.',
            ],
            [
                'name' => 'Carga General',
                'code' => 'CG',
                'description' => 'Embarcación diseñada para transportar carga seca no especializada como cajas, pallets, sacos, etc.',
            ],
            [
                'name' => 'Pasajeros',
                'code' => 'PAS',
                'description' => 'Embarcación diseñada principalmente para el transporte de pasajeros.',
            ],
            [
                'name' => 'Tanquero',
                'code' => 'TAN',
                'description' => 'Embarcación diseñada para el transporte de líquidos a granel, como petróleo, productos químicos o gas natural licuado.',
            ],
            [
                'name' => 'Granelero',
                'code' => 'GRA',
                'description' => 'Embarcación diseñada para transportar cargas secas a granel como cereales, minerales o carbón.',
            ],
            [
                'name' => 'Pesquero',
                'code' => 'PES',
                'description' => 'Embarcación diseñada para la captura de peces u otros recursos marinos.',
            ],
            [
                'name' => 'Mixto',
                'code' => 'MIX',
                'description' => 'Embarcación diseñada para transportar tanto pasajeros como carga.',
            ],
        ];

        foreach ($serviceTypes as $serviceType) {
            ServiceType::create($serviceType);
        }
    }
}
