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
        Schema::create('requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requirement_set_id')->constrained('requirement_sets')->onDelete('cascade');
            $table->enum('type', ['field_required', 'document_required', 'consent_required']);
            $table->string('key'); // driving_licence, proof_of_address, min_age, etc.
            $table->string('label');
            $table->text('description')->nullable();
            $table->json('validation_rules')->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->json('conditions')->nullable(); // When this requirement applies
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('requirement_set_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirements');
    }
};
