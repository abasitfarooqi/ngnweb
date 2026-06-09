<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('purchase_used_vehicles')) {
            Schema::create('purchase_used_vehicles', function (Blueprint $table) {
                $table->bigIncrements('id');

                // Keep types compatible with your seeder dump
                $table->string('purchase_date')->nullable();
                $table->string('full_name');
                $table->string('address');
                $table->string('postcode');
                $table->string('phone_number');
                $table->string('email');

                $table->string('make');
                $table->string('year');
                $table->string('colour');
                $table->string('fuel_type');
                $table->string('model');
                $table->string('reg_no');

                $table->integer('current_mileage')->default(0);
                $table->string('vin');
                $table->string('engine_number')->nullable();

                $table->decimal('price', 10, 2)->default(0);
                $table->decimal('deposit', 10, 2)->default(0);
                $table->decimal('outstanding', 10, 2)->default(0);
                $table->decimal('total_to_pay', 10, 2)->default(0);

                $table->string('account_name')->nullable();
                $table->string('sort_code')->nullable();
                $table->string('account_number')->nullable();

                $table->timestamps();

                $table->unsignedBigInteger('user_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_used_vehicles');
    }
};
