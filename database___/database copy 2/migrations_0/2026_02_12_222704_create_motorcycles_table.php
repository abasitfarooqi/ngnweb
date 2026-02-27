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
        Schema::create('motorcycles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->nullable();
            $table->string('availability')->nullable();
            $table->boolean('sale_new_enquire')->nullable();
            $table->decimal('sale_new_price')->nullable();
            $table->decimal('sale_used_price')->nullable();
            $table->decimal('rental_deposit')->nullable();
            $table->integer('rental_deposit_weeks')->nullable();
            $table->boolean('rental_deposit_paid')->nullable();
            $table->decimal('rental_price')->nullable();
            $table->date('rental_start_date')->nullable();
            $table->date('next_payment_date')->nullable();
            $table->date('npd_test')->nullable();
            $table->bigInteger('rental_id')->nullable();
            $table->boolean('is_insured')->nullable();
            $table->string('registration')->nullable();
            $table->string('registration_place')->nullable();
            $table->string('registration_date')->nullable();
            $table->string('make', 70)->nullable();
            $table->string('model', 70)->nullable();
            $table->string('year', 70)->nullable();
            $table->string('colour')->nullable();
            $table->string('category', 70)->nullable();
            $table->string('description', 1000)->nullable()->default('Null');
            $table->date('road_tax')->nullable();
            $table->date('mot')->nullable();
            $table->date('insurance')->nullable();
            $table->string('vin_number')->nullable();
            $table->string('engine')->nullable();
            $table->string('engine_details')->nullable();
            $table->string('power')->nullable();
            $table->string('torque')->nullable();
            $table->string('compression')->nullable();
            $table->string('bore_x_stroke')->nullable();
            $table->integer('valves_per_cylinder')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('fuel_system')->nullable();
            $table->string('fuel_consumption')->nullable();
            $table->string('lubrication_system')->nullable();
            $table->string('cooling_system')->nullable();
            $table->string('gear_box')->nullable();
            $table->string('clutch')->nullable();
            $table->string('drive_line')->nullable();
            $table->string('co2_emissions', 70)->nullable();
            $table->string('green_house_gases')->nullable();
            $table->string('emission_details')->nullable();
            $table->string('exhaust_system')->nullable();
            $table->string('frame_type')->nullable();
            $table->string('front_brakes')->nullable();
            $table->string('front_brakes_diameter')->nullable();
            $table->string('front_suspension')->nullable();
            $table->string('front_tyre')->nullable();
            $table->string('front_wheel_travel')->nullable();
            $table->string('rake')->nullable();
            $table->string('rear_brakes')->nullable();
            $table->string('rear_brakes_diameter')->nullable();
            $table->string('rear_suspension')->nullable();
            $table->string('rear_tyre')->nullable();
            $table->string('rear_wheel_travel')->nullable();
            $table->string('seat')->nullable();
            $table->string('trail')->nullable();
            $table->string('wheel_plan')->nullable();
            $table->string('alternate_seat_height')->nullable();
            $table->string('dry_weight')->nullable();
            $table->string('fuel_capacity')->nullable();
            $table->string('overall_height')->nullable();
            $table->string('overall_length')->nullable();
            $table->string('power_weight_ratio')->nullable();
            $table->string('reserve_fuel_capacity')->nullable();
            $table->string('seat_height')->nullable();
            $table->string('weight_incl_oil_gas_etc')->nullable();
            $table->string('comments')->nullable();
            $table->string('starter')->nullable();
            $table->string('euro_status', 70)->nullable();
            $table->date('last_v5_issue_date')->nullable();
            $table->string('type_approval')->nullable();
            $table->string('tax_status', 70)->nullable();
            $table->string('tax_due_date', 70)->nullable();
            $table->string('mot_status', 70)->nullable();
            $table->date('mot_expiry_date')->nullable();
            $table->string('month_of_first_registration', 70)->nullable();
            $table->string('marked_for_export', 70)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('auth_user', 70)->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->string('slug')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('type', 70)->nullable();
            $table->string('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorcycles');
    }
};
