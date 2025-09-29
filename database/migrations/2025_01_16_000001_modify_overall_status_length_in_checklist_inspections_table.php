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
            // Modify the overall_status column to allow up to 20 characters
            $table->string('overall_status', 20)->default('A')->change();
        });
    }

    /**\n     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checklist_inspections_complete', function (Blueprint $table) {
            // Revert back to 1 character length
            $table->string('overall_status', 1)->default('A')->change();
        });
    }
};