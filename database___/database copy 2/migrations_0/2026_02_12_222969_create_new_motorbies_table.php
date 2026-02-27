<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('new_motorbikes', function (Blueprint $table) {
            $table->id();
            $table->date('purchase_date')->default(now());
            $table->string('VRM')->default('N/A');
            $table->string('make')->default('N/A');
            $table->string('model')->default('N/A');
            $table->string('colour')->default('N/A');
            $table->string('engine')->default('N/A');
            $table->string('year')->default('N/A');
            $table->string('VIM')->default('N/A');
            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('status')->default('N/A');
            $table->boolean('is_vrm')->default(false);
            $table->boolean('is_migrated')->default(false);
            $table->date('migrated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('new_motorbikes');
    }
};
