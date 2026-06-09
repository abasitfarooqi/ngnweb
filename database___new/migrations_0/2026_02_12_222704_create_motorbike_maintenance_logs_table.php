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
        Schema::create('motorbike_maintenance_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('motorbike_id')->index('motorbike_maintenance_logs_motorbike_id_foreign');
            $table->unsignedBigInteger('booking_id')->nullable()->index('motorbike_maintenance_logs_booking_id_foreign');
            $table->unsignedBigInteger('user_id')->index('motorbike_maintenance_logs_user_id_foreign');
            $table->decimal('cost', 10)->default(0);
            $table->dateTime('serviced_at');
            $table->string('description');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorbike_maintenance_logs');
    }
};
