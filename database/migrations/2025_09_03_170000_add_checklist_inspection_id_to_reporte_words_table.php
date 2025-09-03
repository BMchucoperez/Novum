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
        // Verificar si la tabla existe y no tiene la columna checklist_inspection_id
        if (Schema::hasTable('reporte_words') && !Schema::hasColumn('reporte_words', 'checklist_inspection_id')) {
            Schema::table('reporte_words', function (Blueprint $table) {
                $table->foreignId('checklist_inspection_id')
                    ->after('id')
                    ->nullable()
                    ->constrained('checklist_inspections')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('reporte_words') && Schema::hasColumn('reporte_words', 'checklist_inspection_id')) {
            Schema::table('reporte_words', function (Blueprint $table) {
                $table->dropForeign(['checklist_inspection_id']);
                $table->dropColumn('checklist_inspection_id');
            });
        }
    }
};