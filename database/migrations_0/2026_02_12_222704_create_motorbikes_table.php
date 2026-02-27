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
        Schema::create('motorbikes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('vehicle_profile_id')->default(1)->index('motorbikes_vehicle_profile_id_foreign');
            $table->boolean('is_ebike')->default(false);
            $table->string('vin_number')->unique();
            $table->string('make');
            $table->string('model');
            $table->year('year');
            $table->string('engine');
            $table->string('color');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('co2_emissions')->nullable();
            $table->string('fuel_type')->nullable();
            $table->boolean('marked_for_export')->default(false);
            $table->string('type_approval')->nullable();
            $table->string('wheel_plan')->nullable();
            $table->date('month_of_first_registration')->nullable();
            $table->string('reg_no')->nullable();
            $table->date('date_of_last_v5c_issuance')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable()->index('motorbikes_branch_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorbikes');
    }
};
