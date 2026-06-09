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
        Schema::create('purchase_agreement_accesses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('passcode');
            $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('purchase_id')->index('purchase_agreement_accesses_purchase_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_agreement_accesses');
    }
};
