<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('structure_and_machineries', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_id')->nullable()->after('id');
            $table->unsignedBigInteger('vessel_id')->nullable()->after('owner_id');
            $table->unsignedBigInteger('vessel_2_id')->nullable()->after('vessel_id');
            $table->unsignedBigInteger('vessel_3_id')->nullable()->after('vessel_2_id');
            $table->string('inspector_license')->nullable()->after('inspector_name');
        });
    }

    public function down(): void
    {
        Schema::table('structure_and_machineries', function (Blueprint $table) {
            $table->dropColumn(['owner_id', 'vessel_id', 'vessel_2_id', 'vessel_3_id', 'inspector_license']);
        });
    }
};
