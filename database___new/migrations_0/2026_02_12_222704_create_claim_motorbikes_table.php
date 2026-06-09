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
        Schema::create('claim_motorbikes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('fullname');
            $table->string('email');
            $table->string('phone');
            $table->unsignedBigInteger('motorbike_id')->index('claim_motorbikes_motorbike_id_foreign');
            $table->unsignedBigInteger('branch_id')->index('claim_motorbikes_branch_id_foreign');
            $table->unsignedBigInteger('user_id')->index('claim_motorbikes_user_id_foreign');
            $table->text('notes');
            $table->dateTime('case_date');
            $table->boolean('is_received')->default(false);
            $table->dateTime('received_date')->nullable();
            $table->boolean('is_returned')->default(false);
            $table->dateTime('returned_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_motorbikes');
    }
};
