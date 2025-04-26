<?php

namespace Database\Seeders;

use App\Models\Owner;
use Illuminate\Database\Seeder;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owners = [
            [
                'name' => 'Transportes Fluviales S.A.',
                'type' => 'company',
                'identity_document' => 'RUC 20123456789',
                'contact' => '+51 987654321 / info@transportesfluviales.com',
            ],
            [
                'name' => 'Naviera Amazónica S.A.C.',
                'type' => 'company',
                'identity_document' => 'RUC 20123456790',
                'contact' => '+51 987654322 / contacto@navieraamazonica.com',
            ],
            [
                'name' => 'Juan Carlos Pérez Rodríguez',
                'type' => 'individual',
                'identity_document' => 'DNI 12345678',
                'contact' => '+51 987654323 / juanperez@gmail.com',
            ],
            [
                'name' => 'María Fernanda López Torres',
                'type' => 'individual',
                'identity_document' => 'DNI 87654321',
                'contact' => '+51 987654324 / marialopez@gmail.com',
            ],
            [
                'name' => 'Transportes Marítimos del Perú S.A.',
                'type' => 'company',
                'identity_document' => 'RUC 20123456791',
                'contact' => '+51 987654325 / info@transportesmaritimos.com',
            ],
            [
                'name' => 'Pesquera del Pacífico S.A.C.',
                'type' => 'company',
                'identity_document' => 'RUC 20123456792',
                'contact' => '+51 987654326 / contacto@pesquerapacifico.com',
            ],
            [
                'name' => 'Roberto Alejandro Gómez Silva',
                'type' => 'individual',
                'identity_document' => 'DNI 23456789',
                'contact' => '+51 987654327 / robertogomez@gmail.com',
            ],
            [
                'name' => 'Transportes y Servicios Fluviales E.I.R.L.',
                'type' => 'company',
                'identity_document' => 'RUC 20123456793',
                'contact' => '+51 987654328 / info@serviciosfluviales.com',
            ],
            [
                'name' => 'Ana Lucía Martínez Vega',
                'type' => 'individual',
                'identity_document' => 'DNI 34567890',
                'contact' => '+51 987654329 / anamartinez@gmail.com',
            ],
            [
                'name' => 'Corporación Naviera del Oriente S.A.',
                'type' => 'company',
                'identity_document' => 'RUC 20123456794',
                'contact' => '+51 987654330 / contacto@navieraoriente.com',
            ],
        ];

        foreach ($owners as $owner) {
            Owner::create($owner);
        }
    }
}
