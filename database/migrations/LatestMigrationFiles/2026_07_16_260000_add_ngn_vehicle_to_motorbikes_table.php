<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('motorbikes', 'ngn_vehicle')) {
            Schema::table('motorbikes', function (Blueprint $table) {
                $table->boolean('ngn_vehicle')->default(false)->after('vehicle_profile_id');
            });
        }

        DB::table('motorbikes')
            ->where('vehicle_profile_id', 1)
            ->update(['ngn_vehicle' => 1]);

        $saleMotorbikeIds = DB::table('motorbikes_sale')
            ->whereNotNull('motorbike_id')
            ->distinct()
            ->pluck('motorbike_id');

        if ($saleMotorbikeIds->isNotEmpty()) {
            DB::table('motorbikes')
                ->whereIn('id', $saleMotorbikeIds)
                ->update(['ngn_vehicle' => 1]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('motorbikes', 'ngn_vehicle')) {
            Schema::table('motorbikes', function (Blueprint $table) {
                $table->dropColumn('ngn_vehicle');
            });
        }
    }
};
