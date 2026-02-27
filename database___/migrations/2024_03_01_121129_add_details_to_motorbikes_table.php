<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsToMotorbikesTable extends Migration
{
    public function up()
    {
        Schema::table('motorbikes', function (Blueprint $table) {
            $table->unsignedBigInteger('co2_emissions')->nullable();
            $table->string('fuel_type')->nullable();
            $table->boolean('marked_for_export')->default(false);
            $table->string('type_approval')->nullable();
            $table->string('wheel_plan')->nullable();
            $table->date('month_of_first_registration')->nullable();
        });
    }

    public function down()
    {
        Schema::table('motorbikes', function (Blueprint $table) {
            $table->dropColumn([
                'co2_emissions',
                'fuel_type',
                'marked_for_export',
                'type_approval',
                'wheel_plan',
                'month_of_first_registration',
            ]);
        });
    }
}
