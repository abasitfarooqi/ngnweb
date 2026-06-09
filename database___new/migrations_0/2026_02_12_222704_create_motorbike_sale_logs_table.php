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
        Schema::create('motorbike_sale_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('motorbike_id')->index('motorbike_sale_logs_motorbike_id_foreign');
            $table->unsignedBigInteger('motorbikes_sale_id')->index('motorbike_sale_logs_motorbikes_sale_id_foreign');
            $table->unsignedBigInteger('user_id');
            $table->string('username');
            $table->string('reg_no')->nullable();
            $table->boolean('is_sold');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorbike_sale_logs');
    }
};
