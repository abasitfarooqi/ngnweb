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
        Schema::create('motorbikes_repair', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('arrival_date');
            $table->unsignedBigInteger('motorbike_id')->index('motorbikes_repair_motorbike_id_foreign');
            $table->text('notes');
            $table->boolean('is_repaired')->default(false);
            $table->dateTime('repaired_date')->nullable();
            $table->boolean('is_returned')->default(false);
            $table->dateTime('returned_date')->nullable();
            $table->timestamps();
            $table->string('fullname');
            $table->string('email');
            $table->string('phone');
            $table->unsignedBigInteger('branch_id')->nullable()->index('motorbikes_repair_branch_id_foreign');
            $table->unsignedBigInteger('user_id')->nullable()->index('motorbikes_repair_user_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorbikes_repair');
    }
};
