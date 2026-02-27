<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false)->unique();
            $table->string('code')->unique();
            $table->string('description')->nullable(true);
            $table->string('version')->nullable(false)->default('1.0.0');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_applications');
    }
};
