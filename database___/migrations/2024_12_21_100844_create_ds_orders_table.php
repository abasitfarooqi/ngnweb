<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ds_orders', function (Blueprint $table) {
            $table->id();
            $table->datetime('pick_up_datetime')->default(now())->comment('Unlike Timestamp. It is a date of time pickup');
            $table->string('full_name')->default('')->comment('Name of person who book order');
            $table->string('phone')->default('')->comment('Phone Number of person who book order');
            $table->string('address')->default('')->comment('Person Address who order. It is important regardless pickup address and drop-off address.');
            $table->string('postcode')->default('')->comment('Postcode of person who proceed the order');
            $table->text('note')->nullable()->comment('Additional Note / Special Instruction');
            $table->boolean('proceed')->default(false)->comment('It remain false unless customer pay.');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ds_orders');
    }
};
