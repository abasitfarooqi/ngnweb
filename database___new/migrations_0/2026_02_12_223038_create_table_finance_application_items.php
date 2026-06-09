<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id')->nullable(false);
            $table->foreign('application_id')->references('id')->on('finance_applications')->onDelete('restrict');
            $table->unsignedBigInteger('motorbike_id')->nullable(false);
            $table->foreign('motorbike_id')->references('id')->on('motorbikes')->onDelete('restrict');
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('end_date')->nullable(true);
            $table->boolean('is_posted')->default(false);
            $table->decimal('weekly_instalment', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('application_items');
    }
};
