<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('motorbikes_sold')) {
            Schema::create('motorbikes_sold', function (Blueprint $table) {
                $table->bigIncrements('id');

                $table->unsignedBigInteger('listing_id');
                $table->string('customer_name')->default('-');
                $table->string('phone_number')->default('-');
                $table->decimal('sold_price', 10, 2)->default(0);
                $table->string('address')->default('-');
                $table->text('note')->nullable();

                $table->timestamps();

                $table->index('listing_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbikes_sold');
    }
};
