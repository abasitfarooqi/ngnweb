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
        Schema::create('ngn_partners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('companyname', 50)->unique();
            $table->string('company_logo')->default('/assets/img/no-image.png');
            $table->string('company_address')->nullable();
            $table->string('company_number')->nullable();
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->integer('fleet_size')->nullable();
            $table->string('operating_since')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngn_partners');
    }
};
