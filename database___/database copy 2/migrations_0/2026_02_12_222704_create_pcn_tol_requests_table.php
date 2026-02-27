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
        Schema::create('pcn_tol_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pcn_case_id')->nullable()->index('pcn_tol_requests_pcn_case_id_foreign');
            $table->unsignedBigInteger('update_id')->index('pcn_tol_requests_update_id_foreign');
            $table->date('request_date')->default('2025-08-21');
            $table->enum('status', ['pending', 'sent', 'approved', 'rejected'])->default('pending');
            $table->string('full_path')->nullable();
            $table->timestamp('letter_sent_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable()->index('pcn_tol_requests_user_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pcn_tol_requests');
    }
};
