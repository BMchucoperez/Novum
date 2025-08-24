<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('checklist_inspections_complete', function (Blueprint $table) {
            $table->id();
            
            // General information
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vessel_id')->constrained()->cascadeOnDelete();
            
            // Date fields for inspection tracking
            $table->date('inspection_start_date');
            $table->date('inspection_end_date');
            $table->date('convoy_date');
            
            // Inspector information
            $table->string('inspector_name');
            
            // 6 parts with checklist items (JSON format)
            // Each part contains items with: item, prioridad, checkbox_1, checkbox_2, estado, comentarios, archivos_adjuntos
            for ($i = 1; $i <= 6; $i++) {
                $table->json('parte_' . $i . '_items')->nullable();
            }
            
            // Overall status and observations
            $table->string('overall_status', 1)->default('A'); // V, A, N, R
            $table->text('general_observations')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_inspections_complete');
    }
};
