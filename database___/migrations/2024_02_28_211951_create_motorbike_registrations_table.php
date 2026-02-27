<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('motorbike_registrations')) {
            Schema::create('motorbike_registrations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('motorbike_id')->constrained('motorbikes')->onDelete('cascade');
                $table->string('registration_number');
                $table->boolean('active')->default(true); // Set default to true
                $table->date('start_date');
                $table->date('end_date')->nullable();
                $table->timestamps();
                $table->unique(['motorbike_id', 'registration_number']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbike_registrations');
    }
};
