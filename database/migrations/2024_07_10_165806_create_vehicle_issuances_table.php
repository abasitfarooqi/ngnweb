<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_issuances', function (Blueprint $table) {
            $table->id();
            $table->datetime('issue_date')->nullable(false);
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->foreignId('motorbike_id')->constrained('motorbikes')->onDelete('restrict');
            $table->text('notes')->nullable();
            $table->boolean('is_returned')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_issuances');
    }
};
