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
        Schema::create('finance_applications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id')->index('finance_applications_customer_id_foreign');
            $table->unsignedBigInteger('user_id')->index('finance_applications_user_id_foreign');
            $table->string('sold_by')->nullable()->comment('Person who sold the bike; set once, do not modify');
            $table->boolean('is_posted')->default(false);
            $table->timestamps();
            $table->decimal('deposit', 10)->default(0);
            $table->text('notes')->nullable();
            $table->dateTime('contract_date')->nullable();
            $table->date('first_instalment_date')->nullable();
            $table->decimal('weekly_instalment', 10)->default(0);
            $table->boolean('is_monthly')->default(false);
            $table->decimal('motorbike_price', 10)->default(0);
            $table->text('extra_items')->nullable();
            $table->decimal('extra', 10)->nullable();
            $table->boolean('log_book_sent')->default(false);
            $table->boolean('is_cancelled')->default(false);
            $table->string('reason_of_cancellation')->nullable();
            $table->dateTime('logbook_transfer_date')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->boolean('is_used')->default(false);
            $table->boolean('is_used_extended')->default(false);
            $table->boolean('is_used_extended_custom')->default(false);
            $table->boolean('is_new_latest')->default(false);
            $table->boolean('is_used_latest')->default(false);
            $table->boolean('is_subscription')->default(false);
            $table->string('subscription_option', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_applications');
    }
};
