<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motorbikes_sold', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('listing_id');
            $table->foreign('listing_id')->references('id')->on('motorbikes_sale')->onDelete('restrict');
            $table->string('customer_name');
            $table->string('phone_number');
            $table->decimal('sold_price', 8, 2);
            $table->string('address');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbikes_sold');
    }
};
