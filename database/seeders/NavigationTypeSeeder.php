<?php

namespace Database\Seeders;

use App\Models\NavigationType;
use Illuminate\Database\Seeder;

class NavigationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $navigationTypes = [
            [
                'name' => 'Navegación Fluvial',
                'description' => 'Navegación en ríos, lagos y otras vías navegables interiores.',
            ],
            [
                'name' => 'Navegación Marítima',
                'description' => 'Navegación en mares y océanos, generalmente a lo largo de la costa.',
            ],
            [
                'name' => 'Navegación Lacustre',
                'description' => 'Navegación exclusiva en lagos.',
            ],
            [
                'name' => 'Navegación de Altura',
                'description' => 'Navegación en mar abierto, lejos de la costa.',
            ],
            [
                'name' => 'Navegación Costera',
                'description' => 'Navegación cerca de la costa, generalmente a la vista de tierra.',
            ],
            [
                'name' => 'Navegación Mixta',
                'description' => 'Combinación de navegación fluvial y marítima.',
            ],
        ];

        foreach ($navigationTypes as $navigationType) {
            NavigationType::create($navigationType);
        }
    }
}
