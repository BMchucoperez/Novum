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
        Schema::table('onboard_management_documents', function (Blueprint $table) {
            $table->foreignId('vessel_2_id')->nullable()->after('vessel_id')->constrained('vessels')->onDelete('set null');
            $table->foreignId('vessel_3_id')->nullable()->after('vessel_2_id')->constrained('vessels')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('onboard_management_documents', function (Blueprint $table) {
            $table->dropForeign(['vessel_2_id']);
            $table->dropForeign(['vessel_3_id']);
            $table->dropColumn(['vessel_2_id', 'vessel_3_id']);
        });
    }
};
