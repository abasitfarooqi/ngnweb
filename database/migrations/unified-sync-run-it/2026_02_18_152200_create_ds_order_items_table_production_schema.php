<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ds_order_items')) {
            Schema::create('ds_order_items', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('ds_order_id')->index();

                $table->decimal('pickup_lat', 10, 8);
                $table->decimal('pickup_lon', 10, 8);
                $table->decimal('dropoff_lat', 10, 8);
                $table->decimal('dropoff_lon', 10, 8);

                $table->string('pickup_address')->default('')->comment('Point of Pickup the actual asset Full Address.');
                $table->string('pickup_postcode')->default('')->comment('Point of Pickup the actual asset postcode.');
                $table->string('dropoff_address')->default('')->comment('Point of Dropoff the actual asset Full Address.');
                $table->decimal('distance', 10, 2)->nullable()->comment('Total approx. Distance in miles');
                $table->string('dropoff_postcode')->default('')->comment('Point of Dropoff the actual asset postcode.');

                $table->string('vrm')->default('')->comment('Vehicle Reg No.');
                $table->boolean('moveable')->nullable()->default(false)->comment('Is bike moveable or require lift-up to load and unload?');
                $table->text('documents')->nullable()->comment('All or Essential Documents available?');
                $table->text('keys')->nullable()->comment('All Relevant keys are available?');
                $table->text('note')->nullable()->comment('Additional Note / Special Instruction');

                $table->timestamps();

                $table->foreign('ds_order_id')
                    ->references('id')
                    ->on('ds_orders')
                    ->onUpdate('restrict')
                    ->onDelete('restrict');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ds_order_items');
    }
};
