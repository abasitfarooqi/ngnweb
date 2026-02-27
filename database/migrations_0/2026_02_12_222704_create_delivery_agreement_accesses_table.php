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
        Schema::create('delivery_agreement_accesses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id')->index('delivery_agreement_accesses_customer_id_foreign');
            $table->unsignedBigInteger('enquiry_id')->index('delivery_agreement_accesses_enquiry_id_foreign');
            $table->string('passcode', 32)->unique();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_agreement_accesses');
    }
};
