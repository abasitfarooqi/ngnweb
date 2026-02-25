<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_motorbikes', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('email');
            $table->string('phone');
            $table->foreignId('motorbike_id')->constrained('motorbikes')->onDelete('restrict');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->text('notes')->nullable(false);
            $table->datetime('case_date')->nullable(false);
            $table->boolean('is_received')->default(false);
            $table->datetime('received_date')->nullable();
            $table->boolean('is_returned')->default(false);
            $table->datetime('returned_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_motorbikes');
    }
};
