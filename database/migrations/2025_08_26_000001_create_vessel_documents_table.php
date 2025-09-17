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
        Schema::create('vessel_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade');
            $table->string('document_type', 100);
            $table->enum('document_category', ['bandeira_apolices', 'sistema_gestao']);
            $table->string('document_name');
            $table->string('file_path', 500);
            $table->string('file_name');
            $table->integer('file_size')->unsigned();
            $table->string('mime_type', 50);
            $table->timestamp('uploaded_at');
            $table->boolean('is_valid')->default(true);
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indices para optimización
            $table->index('vessel_id');
            $table->index('document_type');
            $table->index('document_category');
            $table->index('is_valid');
            $table->index('expiry_date');
            
            // Único por embarcación y tipo de documento
            $table->unique(['vessel_id', 'document_type'], 'unique_vessel_document');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vessel_documents');
    }
};