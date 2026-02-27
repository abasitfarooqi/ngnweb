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
            $table->id();
            $table->unsignedBigInteger('motorbike_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('motorbike_id')->references('id')->on('motorbikes')->onDelete('restrict');
            $table->boolean('iscurrent')->default(true)->notNull();
            $table->decimal('weekly_price', 8, 2)->default(0)->notNull();
            $table->timestamp('update_date')->default(DB::raw('CURRENT_TIMESTAMP'))->notNull();
            $table->decimal('minimum_deposit', 8, 2)->default(0)->notNull();
            $table->unique(['motorbike_id', 'iscurrent']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // NO DROP TABLE
        // Schema::dropIfExists('renting_pricings');
    }
};
