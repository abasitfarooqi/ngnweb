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
        Schema::create('email_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id')->index('email_jobs_customer_id_foreign');
            $table->unsignedBigInteger('user_id')->index('email_jobs_user_id_foreign');
            $table->boolean('is_sent')->default(false);
            $table->dateTime('sent_at')->nullable();
            $table->string('template_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_jobs');
    }
};
