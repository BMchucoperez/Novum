<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reporte_words', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('owner_id')->constrained()->onDelete('cascade');
            $table->foreignId('vessel_id')->constrained()->onDelete('cascade');
            $table->foreignId('vessel2_id')->nullable()->constrained('vessels')->onDelete('cascade');
            $table->foreignId('vessel3_id')->nullable()->constrained('vessels')->onDelete('cascade');
            $table->string('inspector_name');
            $table->date('inspection_date');
            $table->json('filters')->nullable();
            $table->string('file_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reporte_words');
    }
};