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
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('dob')->nullable();
            $table->string('email')->unique();
            $table->boolean('is_register')->default(false);
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('postcode')->nullable();
            $table->string('city')->default('London');
            $table->string('country')->default('UK');
            $table->timestamps();
            $table->string('nationality')->nullable();
            $table->text('reputation_note')->nullable();
            $table->string('emergency_contact')->nullable()->comment('Name of the emergency contact person');
            $table->string('whatsapp')->nullable()->comment('Whatsapp number');
            $table->string('Customer Full Name', 50)->nullable();
            $table->string('last name', 50)->nullable();
            $table->integer('PHONE1')->nullable();
            $table->string('creatd', 50)->nullable();
            $table->string('updated', 50)->nullable();
            $table->string('whatsapp_number', 50)->nullable();
            $table->integer('rating')->nullable();
            $table->string('license_number')->nullable();
            $table->date('license_expiry_date')->nullable();
            $table->string('license_issuance_authority')->nullable();
            $table->date('license_issuance_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
