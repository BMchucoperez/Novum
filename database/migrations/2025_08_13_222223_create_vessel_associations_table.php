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
        Schema::create('vessel_associations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('main_vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->foreignId('associated_vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->timestamps();
            
            // Evitar duplicados y auto-asociaciones
            $table->unique(['main_vessel_id', 'associated_vessel_id']);
            $table->index(['main_vessel_id']);
            $table->index(['associated_vessel_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vessel_associations');
    }
};
