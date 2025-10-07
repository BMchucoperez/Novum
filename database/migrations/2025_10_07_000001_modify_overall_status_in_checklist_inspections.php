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
        Schema::table('checklist_inspections', function (Blueprint $table) {
            // Modificar la columna overall_status para permitir hasta 20 caracteres
            $table->string('overall_status', 20)->default('APTO')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checklist_inspections', function (Blueprint $table) {
            // Revertir a 1 carácter
            $table->string('overall_status', 1)->default('A')->change();
        });
    }
};
