<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ngn_partners', function (Blueprint $table) {
            $table->id();
            $table->string('companyname', 50)->unique();
            $table->string('company_logo', 255)->default('/assets/img/no-image.png');
            $table->string('company_address', 255)->nullable();
            $table->string('company_number', 255)->nullable();
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website', 255)->nullable();
            $table->integer('fleet_size')->nullable();
            $table->string('operating_since', 255)->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ngn_partners');
    }
};
