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
        Schema::create('footers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('number', 125)->nullable();
            $table->text('short_description')->nullable();
            $table->string('adress', 125)->nullable();
            $table->string('email', 125)->nullable();
            $table->string('facebook', 125)->nullable();
            $table->string('twitter', 125)->nullable();
            $table->string('copyright', 125)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footers');
    }
};
