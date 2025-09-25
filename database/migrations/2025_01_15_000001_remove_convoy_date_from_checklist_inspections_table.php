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
        Schema::table('checklist_inspections_complete', function (Blueprint $table) {
            $table->dropColumn('convoy_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checklist_inspections_complete', function (Blueprint $table) {
            $table->date('convoy_date')->after('inspection_end_date');
        });
    }
};