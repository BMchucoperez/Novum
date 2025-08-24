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
        Schema::table('reporte_words', function (Blueprint $table) {
            $table->string('inspector_license')->nullable()->after('inspector_name');
            $table->json('associated_vessels')->nullable()->after('vessel_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reporte_words', function (Blueprint $table) {
            $table->dropColumn(['inspector_license', 'associated_vessels']);
        });
    }
};
