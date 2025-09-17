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
        Schema::table('structure_and_machineries', function (Blueprint $table) {
            // Agregar columnas de estado para cada una de las 13 partes
            for ($i = 1; $i <= 13; $i++) {
                $table->string('parte_' . $i . '_estado')->default('A')->after('parte_' . $i . '_items');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('structure_and_machineries', function (Blueprint $table) {
            // Eliminar columnas de estado para cada una de las 13 partes
            for ($i = 1; $i <= 13; $i++) {
                $table->dropColumn('parte_' . $i . '_estado');
            }
        });
    }
};
