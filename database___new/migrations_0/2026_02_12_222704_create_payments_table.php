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
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('payment_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('motorcycle_id')->nullable();
            $table->string('registration')->nullable();
            $table->string('payment_type')->nullable();
            $table->decimal('rental_deposit')->nullable();
            $table->decimal('rental_price')->nullable();
            $table->string('description')->nullable();
            $table->decimal('received')->nullable();
            $table->decimal('outstanding')->nullable();
            $table->longText('notes')->nullable();
            $table->dateTime('payment_due_date')->nullable();
            $table->bigInteger('payment_due_count')->nullable();
            $table->dateTime('payment_next_date')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->string('paid', 70)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('auth_user')->nullable();
            $table->string('deleted_by')->nullable()->default('');
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
