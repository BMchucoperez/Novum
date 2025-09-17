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
        // Verificar si la tabla existe
        if (Schema::hasTable('reporte_words')) {
            // Verificar que la tabla tiene las columnas report_path y file_path
            if (Schema::hasColumn('reporte_words', 'report_path') && !Schema::hasColumn('reporte_words', 'file_path')) {
                // La tabla tiene report_path pero no file_path
                Schema::table('reporte_words', function (Blueprint $table) {
                    $table->string('file_path')->nullable()->after('report_path');
                });
            } else if (!Schema::hasColumn('reporte_words', 'report_path') && Schema::hasColumn('reporte_words', 'file_path')) {
                // La tabla tiene file_path pero no report_path
                Schema::table('reporte_words', function (Blueprint $table) {
                    $table->string('report_path')->nullable()->after('file_path');
                });
            }
            
            // Verificar la existencia de las columnas generated_by y generated_at
            if (!Schema::hasColumn('reporte_words', 'generated_by')) {
                Schema::table('reporte_words', function (Blueprint $table) {
                    $table->string('generated_by')->nullable();
                });
            }
            
            if (!Schema::hasColumn('reporte_words', 'generated_at')) {
                Schema::table('reporte_words', function (Blueprint $table) {
                    $table->timestamp('generated_at')->nullable();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No se revierte esta migración ya que son columnas necesarias
    }
};