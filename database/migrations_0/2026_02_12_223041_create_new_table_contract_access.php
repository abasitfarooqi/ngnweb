<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_access', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('passcode');
            $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('application_id')->nullable(false);
            $table->foreign('application_id')->references('id')->on('finance_applications')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_access');
    }
};
