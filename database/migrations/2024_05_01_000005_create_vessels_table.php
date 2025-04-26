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
        Schema::create('vessels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('service_type_id')->constrained('service_types');
            $table->foreignId('navigation_type_id')->constrained('navigation_types');
            $table->string('flag_registry');
            $table->string('port_registry');
            $table->year('construction_year');
            $table->foreignId('shipyard_id')->constrained('shipyards');
            $table->decimal('length', 8, 2); // eslora
            $table->decimal('beam', 8, 2);   // manga
            $table->decimal('depth', 8, 2);  // puntal
            $table->decimal('gross_tonnage', 10, 2); // arqueo bruto
            $table->string('registration_number'); // matrícula
            $table->foreignId('owner_id')->constrained('owners');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vessels');
    }
};
