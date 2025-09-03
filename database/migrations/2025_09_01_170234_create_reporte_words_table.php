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
        // Esta tabla ya ha sido creada por una migración anterior (2025_06_27_000000_create_reporte_words_table.php)
        // Solo ejecutaremos esta migración si la tabla no existe
        if (!Schema::hasTable('reporte_words')) {
            Schema::create('reporte_words', function (Blueprint $table) {
                $table->id();
                $table->foreignId('checklist_inspection_id')->constrained('checklist_inspections')->onDelete('cascade');
                $table->string('report_path');
                $table->string('generated_by');
                $table->timestamp('generated_at');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No eliminamos la tabla aquí ya que podría ser utilizada por la migración original
        // Schema::dropIfExists('reporte_words');
    }
};