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
        Schema::create('booking_closing', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('booking_id')->index('booking_closing_booking_id_foreign');
            $table->text('notice_details')->nullable();
            $table->boolean('notice_checked')->default(false);
            $table->text('collect_details')->nullable();
            $table->date('collect_date')->nullable();
            $table->time('collect_time')->nullable();
            $table->boolean('collect_checked')->default(false);
            $table->boolean('damages_checked')->default(false);
            $table->boolean('pcn_checked')->default(false);
            $table->boolean('pending_checked')->default(false);
            $table->boolean('deposit_checked')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_closing');
    }
};
