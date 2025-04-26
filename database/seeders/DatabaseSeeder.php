<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuario de prueba
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Ejecutar los seeders en orden para mantener las relaciones
        $this->call([
            ServiceTypeSeeder::class,    // Primero los tipos de servicio
            NavigationTypeSeeder::class, // Luego los tipos de navegación
            ShipyardSeeder::class,       // Después los astilleros
            OwnerSeeder::class,          // Luego los propietarios
            VesselSeeder::class,         // Finalmente las embarcaciones
        ]);
    }
}
