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
        Schema::create('booking_issuance_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('booking_item_id')->index();
            $table->unsignedBigInteger('issued_by_user_id')->index('booking_issuance_items_issued_by_user_id_foreign');
            $table->integer('current_mileage');
            $table->boolean('is_video_recorded')->default(false);
            $table->boolean('accessories_checked')->default(false);
            $table->string('issuance_branch', 20);
            $table->timestamps();
            $table->text('notes')->nullable();
            $table->boolean('is_insured')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_issuance_items');
    }
};
