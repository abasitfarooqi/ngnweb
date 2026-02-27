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
            $table->id();
            $table->foreignId('update_id')->constrained('pcn_case_updates'); // links to PcnCaseUpdate
            $table->date('request_date')->default(now());
            $table->enum('status', ['pending', 'sent', 'approved', 'rejected'])->default('pending');
            $table->timestamp('letter_sent_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
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
