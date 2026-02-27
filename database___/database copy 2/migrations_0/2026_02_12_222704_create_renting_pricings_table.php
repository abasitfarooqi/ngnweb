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
        Schema::create('renting_pricings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('motorbike_id');
            $table->unsignedBigInteger('user_id')->index('renting_pricings_user_id_foreign');
            $table->boolean('iscurrent')->default(true);
            $table->decimal('weekly_price')->default(0);
            $table->timestamp('update_date')->useCurrent();
            $table->decimal('minimum_deposit')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renting_pricings');
    }
};
