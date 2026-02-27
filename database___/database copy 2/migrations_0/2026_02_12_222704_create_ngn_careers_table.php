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
            $table->bigIncrements('id');
            $table->string('job_title');
            $table->text('description');
            $table->string('employment_type');
            $table->string('location');
            $table->string('salary')->nullable();
            $table->string('contact_email');
            $table->date('job_posted')->nullable();
            $table->date('expire_date')->nullable();
            $table->timestamps();
            $table->boolean('is_active')->default(false);
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
