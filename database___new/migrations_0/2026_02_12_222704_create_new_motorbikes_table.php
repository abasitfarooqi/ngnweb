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
        Schema::create('new_motorbikes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('purchase_date')->default('2024-09-25');
            $table->string('VRM')->default('N/A');
            $table->string('make')->default('N/A');
            $table->string('model')->default('N/A');
            $table->string('colour')->default('N/A');
            $table->string('engine')->default('N/A');
            $table->string('year')->default('N/A');
            $table->string('VIM')->default('N/A');
            $table->unsignedBigInteger('branch_id')->index('new_motorbikes_branch_id_foreign');
            $table->unsignedBigInteger('user_id')->index('new_motorbikes_user_id_foreign');
            $table->string('status')->default('N/A');
            $table->boolean('is_vrm')->default(false);
            $table->boolean('is_migrated')->default(false);
            $table->date('migrated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_motorbikes');
    }
};
