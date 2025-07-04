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
        Schema::create('onboard_management_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->string('inspection_type')->default('Documentos del Sistema de Gestión a Bordo');
            $table->date('inspection_date');
            $table->string('inspector_name');
            $table->string('inspector_license')->nullable();
            
            // Parte 1 - 19 items
            $table->json('parte_1_items')->nullable(); // Array de objetos {item, estado, comentarios}
            
            // Parte 2 - 3 items
            $table->json('parte_2_items')->nullable();
            
            // Parte 3 - 5 items
            $table->json('parte_3_items')->nullable();
            
            $table->enum('overall_status', ['V', 'A', 'N', 'R'])->default('A');
            $table->text('general_observations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboard_management_documents');
    }
};
