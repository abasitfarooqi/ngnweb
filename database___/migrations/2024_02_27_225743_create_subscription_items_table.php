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
        if (! Schema::hasTable('subscription_items')) {
            Schema::create('subscription_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('subscription_id');
                $table->string('stripe_id');
                $table->string('stripe_plan');
                $table->integer('quantity');
                $table->timestamps();

                $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_items');
    }
};
