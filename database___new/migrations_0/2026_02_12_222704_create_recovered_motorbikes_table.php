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
        Schema::create('recovered_motorbikes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('case_date');
            $table->unsignedBigInteger('user_id')->index('recovered_motorbikes_user_id_foreign');
            $table->unsignedBigInteger('branch_id')->index('recovered_motorbikes_branch_id_foreign');
            $table->unsignedBigInteger('motorbike_id')->index('recovered_motorbikes_motorbike_id_foreign');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->date('returned_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recovered_motorbikes');
    }
};
