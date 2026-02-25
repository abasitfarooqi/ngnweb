<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pcn_email_jobs', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_sent')->default(false);
            $table->datetime('sent_at')->nullable();
            $table->string('template_code');
            $table->unsignedBigInteger('motorbike_id');
            $table->foreign('motorbike_id')->references('id')->on('motorbikes');
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->unsignedBigInteger('case_id');
            $table->foreign('case_id')->references('id')->on('pcn_cases');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->boolean('force_stop')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pcn_email_jobs');
    }
};
