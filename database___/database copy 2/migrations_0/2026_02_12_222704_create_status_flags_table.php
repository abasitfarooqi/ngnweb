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
        Schema::create('status_flags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('short_name')->unique();
            $table->string('long_name')->default('-');
            $table->string('color')->default('#ffffff');
            $table->text('icon');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_flags');
    }
};
