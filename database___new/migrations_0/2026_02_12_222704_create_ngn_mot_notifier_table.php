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
        Schema::create('ngn_mot_notifier', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('motorbike_id')->nullable()->index('ngn_mot_notifier_motorbike_id_foreign');
            $table->string('motorbike_reg', 20);
            $table->date('mot_due_date')->nullable();
            $table->date('tax_due_date')->nullable();
            $table->date('insurance_due_date')->nullable();
            $table->string('mot_status')->nullable();
            $table->string('customer_name');
            $table->string('customer_contact', 20);
            $table->string('customer_email');
            $table->boolean('mot_notify_email')->default(true);
            $table->boolean('mot_notify_phone')->default(false);
            $table->boolean('mot_is_email_sent')->default(false);
            $table->boolean('mot_email_sent_expired')->default(false);
            $table->boolean('mot_is_phone_sent')->default(false);
            $table->boolean('mot_is_whatsapp_sent')->default(false);
            $table->boolean('mot_is_notified_30')->default(false);
            $table->boolean('mot_email_sent_30')->default(false);
            $table->boolean('mot_is_notified_10')->default(false);
            $table->boolean('mot_email_sent_10')->default(false);
            $table->date('mot_last_email_notification_date')->nullable();
            $table->date('mot_last_phone_notification_date')->nullable();
            $table->date('mot_last_whatsapp_notification_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngn_mot_notifier');
    }
};
