<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motorcycles', function (Blueprint $table) {
            $table->id();
            $table->string('make', 70)->nullable();
            $table->string('model', 70)->nullable();
            $table->string('category', 70)->nullable();
            $table->string('year', 70)->nullable();
            $table->string('description', 320);
            $table->string('availability')->default('for sale');
            $table->boolean('sale_new_enquire');
            $table->decimal('sale_used_price', 8, 2)->nullable();
            $table->decimal('rental_price', 8, 2)->nullable();
            $table->Integer('user_id')->nullable();
            $table->string('vin_number', 255)->nullable();
            $table->string('motorcycle_reg', 255)->nullable();
            $table->string('displacement', 255)->nullable();
            $table->string('engine_details', 255)->nullable();
            $table->string('power', 255)->nullable();
            $table->string('torque', 255)->nullable();
            $table->string('compression', 255)->nullable();
            $table->string('bore_x_stroke', 255)->nullable();
            $table->integer('valves_per_cylinder')->nullable();
            $table->string('fuel_system', 255)->nullable();
            $table->string('lubrication_system', 255)->nullable();
            $table->string('cooling_system', 255)->nullable();
            $table->string('gear_box', 255)->nullable();
            $table->string('clutch', 255)->nullable();
            $table->string('drive_line', 255)->nullable();
            $table->string('fuel_consumption', 255)->nullable();
            $table->string('green_house_gases', 255)->nullable();
            $table->string('emission_details', 255)->nullable();
            $table->string('exhaust_system', 255)->nullable();
            $table->string('frame_type', 255)->nullable();
            $table->string('front_brakes', 255)->nullable();
            $table->string('front_brakes_diameter', 255)->nullable();
            $table->string('front_suspension', 255)->nullable();
            $table->string('front_tyre', 255)->nullable();
            $table->string('front_wheel_travel', 255)->nullable();
            $table->string('rake', 255)->nullable();
            $table->string('rear_brakes', 255)->nullable();
            $table->string('rear_brakes_diameter', 255)->nullable();
            $table->string('rear_suspension', 255)->nullable();
            $table->string('rear_tyre', 255)->nullable();
            $table->string('rear_wheel_travel', 255)->nullable();
            $table->string('seat', 255)->nullable();
            $table->string('trail', 255)->nullable();
            $table->string('wheels', 255)->nullable();
            $table->string('alternate_seat_height', 255)->nullable();
            $table->string('dry_weight', 255)->nullable();
            $table->string('fuel_capacity', 255)->nullable();
            $table->string('overall_height', 255)->nullable();
            $table->string('overall_length', 255)->nullable();
            $table->string('power_weight_ratio', 255)->nullable();
            $table->string('reserve_fuel_capacity', 255)->nullable();
            $table->string('seat_height', 255)->nullable();
            $table->string('weight_incl_oil_gas_etc', 255)->nullable();
            $table->string('color_options', 255)->nullable();
            $table->string('comments', 255)->nullable();
            $table->string('starter', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('motorcycles');
    }
};
