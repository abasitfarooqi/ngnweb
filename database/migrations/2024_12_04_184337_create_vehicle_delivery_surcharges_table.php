<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_delivery_surcharges', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique()->comment('Type of surcharge (e.g. motorcycle type fees, time surcharges, etc)');
            $table->decimal('percentage', 5, 2)->nullable()->comment('Percentage surcharge to apply to the total delivery fee');
            $table->timestamps();
        });

        DB::table('vehicle_delivery_surcharges')->insert([
            [
                'type' => 'Time Surcharge - Peak Hours (7 AM - 9 AM, 5 PM - 8 PM)',
                'percentage' => 20.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'Time Surcharge - Night-Time (9 PM - 7 AM)',
                'percentage' => 25.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'Weekend or Public Holiday Surcharge',
                'percentage' => 10.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_delivery_surcharges');
    }
};
