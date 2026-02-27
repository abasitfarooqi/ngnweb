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
        Schema::create('pcn_email_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('is_sent')->default(false);
            $table->dateTime('sent_at')->nullable();
            $table->string('template_code');
            $table->unsignedBigInteger('motorbike_id')->index('pcn_email_jobs_motorbike_id_foreign');
            $table->unsignedBigInteger('customer_id')->index('pcn_email_jobs_customer_id_foreign');
            $table->unsignedBigInteger('case_id')->index('pcn_email_jobs_case_id_foreign');
            $table->unsignedBigInteger('user_id')->index('pcn_email_jobs_user_id_foreign');
            $table->boolean('force_stop')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pcn_email_jobs');
    }
};
