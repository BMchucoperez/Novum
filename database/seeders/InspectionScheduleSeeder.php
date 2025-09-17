<?php

namespace Database\Seeders;

use App\Models\InspectionSchedule;
use App\Models\Vessel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class InspectionScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunas embarcaciones existentes
        $vessels = Vessel::take(5)->get();
        
        if ($vessels->isEmpty()) {
            $this->command->info('No vessels found. Please create some vessels first.');
            return;
        }
        
        // Crear inspecciones de prueba para el mes actual
        $statuses = ['scheduled', 'completed', 'cancelled'];
        
        for ($i = 0; $i < 20; $i++) {
            $vessel = $vessels->random();
            $date = Carbon::now()->addDays(rand(-15, 30));
            
            InspectionSchedule::create([
                'title' => 'Inspección de ' . $vessel->name,
                'description' => 'Inspección rutinaria de certificados y documentos estatutarios',
                'start_datetime' => $date->setTime(rand(8, 16), 0, 0),
                'end_datetime' => $date->copy()->addHours(rand(1, 4)),
                'location' => 'Puerto de ' . $vessel->port_registry,
                'inspector_name' => 'Inspector ' . fake()->name(),
                'status' => $statuses[array_rand($statuses)],
                'vessel_id' => $vessel->id,
            ]);
        }
        
        $this->command->info('Inspection schedules created successfully.');
    }
}
