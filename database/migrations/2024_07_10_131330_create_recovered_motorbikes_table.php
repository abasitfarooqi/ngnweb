<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recovered_motorbikes', function (Blueprint $table) {
            $table->id();
            $table->datetime('case_date')->nullable(false);
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->foreignId('motorbike_id')->constrained('motorbikes')->onDelete('restrict');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovered_motorbikes');
    }
};
