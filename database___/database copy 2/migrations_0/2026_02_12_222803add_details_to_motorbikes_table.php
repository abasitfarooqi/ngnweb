<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = ['co2_emissions', 'fuel_type', 'marked_for_export', 'type_approval', 'wheel_plan', 'month_of_first_registration'];
        $add = array_filter($columns, fn ($c) => ! Schema::hasColumn('motorbikes', $c));
        if (empty($add)) {
            return;
        }
        Schema::table('motorbikes', function (Blueprint $table) use ($add) {
            if (in_array('co2_emissions', $add)) {
                $table->string('co2_emissions')->nullable();
            }
            if (in_array('fuel_type', $add)) {
                $table->string('fuel_type')->nullable();
            }
            if (in_array('marked_for_export', $add)) {
                $table->boolean('marked_for_export')->default(false);
            }
            if (in_array('type_approval', $add)) {
                $table->string('type_approval')->nullable();
            }
            if (in_array('wheel_plan', $add)) {
                $table->string('wheel_plan')->nullable();
            }
            if (in_array('month_of_first_registration', $add)) {
                $table->date('month_of_first_registration')->nullable();
            }
        });
    }

    public function down(): void
    {
        $columns = ['co2_emissions', 'fuel_type', 'marked_for_export', 'type_approval', 'wheel_plan', 'month_of_first_registration'];
        $drop = array_filter($columns, fn ($c) => Schema::hasColumn('motorbikes', $c));
        if (empty($drop)) {
            return;
        }
        Schema::table('motorbikes', function (Blueprint $table) use ($drop) {
            $table->dropColumn($drop);
        });
    }
};
