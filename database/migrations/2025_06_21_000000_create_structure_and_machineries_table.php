<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('structure_and_machineries', function (Blueprint $table) {
            $table->id();
            $table->string('inspector_name');
            $table->date('inspection_date');
            // Partes 1 a 13 como JSON
            for ($i = 1; $i <= 13; $i++) {
                $table->json('parte_' . $i . '_items')->nullable();
            }
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('structure_and_machineries');
    }
};
