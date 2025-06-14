<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero, cambiar la columna a varchar temporalmente
        Schema::table('statutory_certificates', function (Blueprint $table) {
            $table->string('overall_status_temp', 10)->nullable();
        });

        // Migrar los datos existentes
        DB::table('statutory_certificates')->update([
            'overall_status_temp' => DB::raw("
                CASE overall_status
                    WHEN 'conforme' THEN 'V'
                    WHEN 'no_conforme' THEN 'R'
                    WHEN 'pendiente' THEN 'A'
                    ELSE 'A'
                END
            ")
        ]);

        // Eliminar la columna antigua
        Schema::table('statutory_certificates', function (Blueprint $table) {
            $table->dropColumn('overall_status');
        });

        // Renombrar la columna temporal y aplicar el nuevo enum
        Schema::table('statutory_certificates', function (Blueprint $table) {
            $table->renameColumn('overall_status_temp', 'overall_status');
        });

        // Cambiar a enum con los nuevos valores
        Schema::table('statutory_certificates', function (Blueprint $table) {
            $table->enum('overall_status', ['V', 'A', 'N', 'R'])->default('A')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cambiar de vuelta a varchar temporalmente
        Schema::table('statutory_certificates', function (Blueprint $table) {
            $table->string('overall_status_temp', 20)->nullable();
        });

        // Migrar los datos de vuelta
        DB::table('statutory_certificates')->update([
            'overall_status_temp' => DB::raw("
                CASE overall_status
                    WHEN 'V' THEN 'conforme'
                    WHEN 'A' THEN 'pendiente'
                    WHEN 'N' THEN 'no_conforme'
                    WHEN 'R' THEN 'no_conforme'
                    ELSE 'pendiente'
                END
            ")
        ]);

        // Eliminar la columna actual
        Schema::table('statutory_certificates', function (Blueprint $table) {
            $table->dropColumn('overall_status');
        });

        // Renombrar y aplicar el enum anterior
        Schema::table('statutory_certificates', function (Blueprint $table) {
            $table->renameColumn('overall_status_temp', 'overall_status');
        });

        Schema::table('statutory_certificates', function (Blueprint $table) {
            $table->enum('overall_status', ['conforme', 'no_conforme', 'pendiente'])->default('pendiente')->change();
        });
    }
};
