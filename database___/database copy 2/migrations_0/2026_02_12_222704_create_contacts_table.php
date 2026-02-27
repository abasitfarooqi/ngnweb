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
        Schema::create('contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 125)->nullable();
            $table->string('email', 125)->nullable();
            $table->string('subject', 125)->nullable();
            $table->string('phone', 125)->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
            $table->string('reg_no')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
