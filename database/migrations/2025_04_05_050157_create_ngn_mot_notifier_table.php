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
            $table->id();
            $table->foreignId('motorbike_id')->constrained('motorbikes');

            $table->string('motorbike_reg', 20); // registration number of the motorbike

            $table->date('mot_due_date')->nullable(); // date of MOT due

            $table->date('tax_due_date')->nullable(); // date of tax due

            $table->date('insurance_due_date')->nullable(); // date of insurance due

            $table->string('mot_status')->nullable(); // status of the MOT  mot_status	string	Valid / Not valid / No details held by DVLA

            $table->string('customer_name', 255); // name of the customer

            $table->string('customer_contact', 20); // contact number of the customer

            $table->string('customer_email', 255); // email of the customer

            $table->boolean('mot_notify_email')->default(true); // true if email notification is enabled

            $table->boolean('mot_notify_phone')->default(false); // true if phone notification is enabled

            $table->boolean('mot_is_email_sent')->default(false); // true if email notification sent

            $table->boolean('mot_is_phone_sent')->default(false); // true if phone notification sent

            $table->boolean('mot_is_notified_30')->default(false); // true if notified for MOT due in 30 days

            $table->boolean('mot_is_notified_10')->default(false); // true if notified for MOT due in 10 days

            $table->date('mot_last_email_notification_date')->nullable(); // date of last email notification

            $table->date('mot_last_phone_notification_date')->nullable(); // date of last phone notification

            $table->text('notes')->nullable(); // notes for the MOT
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
