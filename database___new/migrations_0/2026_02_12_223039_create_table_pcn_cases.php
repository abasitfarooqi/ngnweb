<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pcn_cases', function (Blueprint $table) {
            $table->id();
            $table->string('pcn_number');
            $table->date('date_of_contravention');
            $table->time('time_of_contravention');
            $table->unsignedBigInteger('motorbike_id');
            $table->foreign('motorbike_id')->references('id')->on('motorbikes')->onDelete('restrict');
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('restrict');
            $table->boolean('isClosed')->default(false);
            $table->decimal('full_amount', 10, 2);
            $table->decimal('reduced_amount', 10, 2)->nullable();
            $table->string('picture_url')->nullable();
            $table->string('note')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pcn_cases');
    }
};
