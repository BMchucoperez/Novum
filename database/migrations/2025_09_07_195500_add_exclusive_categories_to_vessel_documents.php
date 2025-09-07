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
        Schema::table('vessel_documents', function (Blueprint $table) {
            // Cambiar de enum a varchar para mayor flexibilidad
            $table->string('document_category', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vessel_documents', function (Blueprint $table) {
            // Revertir al enum original
            $table->enum('document_category', [
                'bandeira_apolices', 
                'sistema_gestao'
            ])->change();
        });
    }
};