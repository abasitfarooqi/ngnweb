<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('motorbikes', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_profile_id')->nullable(false)->after('id')->default(1);
            $table->foreign('vehicle_profile_id')->references('id')->on('vehicle_profiles');
        });
    }

    public function down(): void
    {
        Schema::table('motorbikes', function (Blueprint $table) {
            $table->dropForeign(['vehicle_profile_id']);
            $table->dropColumn('vehicle_profile_id');
        });
    }
};
