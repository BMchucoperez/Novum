<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StructureAndMachinery;

class UpdateStructureAndMachineryStates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'structure-machinery:update-states';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza los estados de todas las partes y estados generales de los registros de Estructura y Maquinaria';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Actualizando estados de Estructura y Maquinaria...');
        
        $records = StructureAndMachinery::all();
        $updated = 0;
        
        foreach ($records as $record) {
            // Recalcular y guardar los estados
            $record->overall_status = $record->calculateOverallStatus();
            $record->save();
            $updated++;
            
            $this->line("Actualizado registro ID: {$record->id}");
        }
        
        $this->info("✅ Se actualizaron {$updated} registros exitosamente.");
        
        return Command::SUCCESS;
    }
}
