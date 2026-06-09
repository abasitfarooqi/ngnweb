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
        Schema::create('booking_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('booking_id')->index('booking_invoices_booking_id_foreign');
            $table->date('invoice_date')->nullable();
            $table->decimal('amount', 10)->default(0);
            $table->date('paid_date')->nullable();
            $table->string('state')->default('DRAFT');
            $table->text('notes')->nullable();
            $table->boolean('is_posted')->default(false);
            $table->boolean('is_paid')->default(false);
            $table->dateTime('notified_at')->nullable();
            $table->unsignedBigInteger('user_id')->index('booking_invoices_user_id_foreign');
            $table->timestamps();
            $table->decimal('deposit', 10)->default(0);
            $table->boolean('is_whatsapp_sent')->nullable()->default(false);
            $table->dateTime('whatsapp_last_reminder_sent_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_invoices');
    }
};
