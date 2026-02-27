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
        Schema::create('ngn_careers', function (Blueprint $table) {
            $table->id();
            $table->string('job_title');
            $table->text('description');
            $table->string('employment_type'); // full-time, part-time, contract, etc.
            $table->string('location');
            $table->decimal('salary', 10, 2)->nullable(); // optional salary field
            $table->string('contact_email');
            $table->date('job_posted')->nullable(); // Add job posted date
            $table->date('expire_date')->nullable(); // Add expire date
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngn_careers');
    }
};
