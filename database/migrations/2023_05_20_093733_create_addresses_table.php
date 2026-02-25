<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('street_address');
            $table->string('street_address_plus')->nullable();
            $table->string('post_code');
            $table->string('city');
            $table->string('phone_number')->nullable();
            $table->boolean('is_default')->default(false);
            $table->enum('type', ['billing', 'shipping']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
};
