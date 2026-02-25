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
        if (! Schema::hasTable('payments')) {

            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('payment_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('motorcycle_id')->nullable();
                $table->string('registration')->nullable();
                $table->string('payment_type')->nullable();
                $table->float('rental_deposit')->nullable();
                $table->float('rental_price')->nullable();
                $table->string('description')->nullable();
                $table->float('received')->nullable();
                $table->float('outstanding')->nullable();
                $table->string('notes')->nullable();
                $table->timestamp('payment_due_date')->nullable();
                $table->integer('payment_due_count')->nullable();
                $table->timestamp('payment_next_date')->nullable();
                $table->timestamp('payment_date')->nullable();
                $table->string('paid')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->string('auth_user')->nullable();
                $table->string('deleted_by')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
