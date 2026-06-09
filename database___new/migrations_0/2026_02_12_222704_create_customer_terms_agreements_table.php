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
        Schema::create('customer_terms_agreements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id')->index('customer_terms_agreements_customer_id_foreign');
            $table->unsignedBigInteger('terms_version_id')->index('customer_terms_agreements_terms_version_id_foreign');
            $table->timestamp('agreed_at')->useCurrentOnUpdate()->useCurrent();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_terms_agreements');
    }
};
