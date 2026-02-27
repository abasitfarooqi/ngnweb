<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('user_id')->nullable();
                $table->string('registration')->nullable();
                $table->string('payment_type')->nullable();
                $table->decimal('rental_deposit', 8, 2)->nullable();
                $table->decimal('rental_price', 8, 2)->nullable();
                $table->text('description')->nullable();
                $table->decimal('received', 8, 2)->nullable();
                $table->decimal('outstanding', 8, 2)->nullable();
                $table->longText('notes')->nullable();
                $table->dateTime('payment_due_date')->nullable();
                $table->bigInteger('payment_due_count')->nullable();
                $table->dateTime('payment_next_date')->nullable();
                $table->dateTime('payment_date')->nullable();
                $table->string('paid');
                $table->string('auth_user')->nullable();
                $table->dateTime('deleted_at')->nullable();
                $table->string('deleted_by')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
