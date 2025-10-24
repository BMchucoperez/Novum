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
        if (Schema::hasTable('reporte_words') && !Schema::hasColumn('reporte_words', 'pdf_path')) {
            Schema::table('reporte_words', function (Blueprint $table) {
                $table->string('pdf_path')->nullable()->after('report_path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('reporte_words') && Schema::hasColumn('reporte_words', 'pdf_path')) {
            Schema::table('reporte_words', function (Blueprint $table) {
                $table->dropColumn('pdf_path');
            });
        }
    }
};

