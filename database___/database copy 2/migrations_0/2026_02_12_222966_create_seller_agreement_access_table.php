<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_agreement_accesses', function (Blueprint $table) {
            $table->id();
            $table->string('passcode');
            $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('purchase_id')->nullable(false);
            $table->foreign('purchase_id')->references('id')->on('purchase_used_vehicles')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_agreement_accesses');
    }
};
