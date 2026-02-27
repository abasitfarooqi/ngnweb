<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateUserAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_addresses')) {
            Schema::create('user_addresses', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->string('last_name');
                $table->string('first_name');
                $table->string('company_name')->nullable();
                $table->string('street_address');
                $table->string('street_address_plus')->nullable();
                $table->string('zipcode');
                $table->string('city');
                $table->string('phone_number')->nullable();
                $table->boolean('is_default')->default(false);
                $table->enum('type', ['billing', 'shipping']);

                $table->unsignedBigInteger('country_id');
                $table->unsignedBigInteger('user_id');
                
                $table->foreign('country_id')->references('id')->on('system_countries');
                $table->foreign('user_id')->references('id')->on('users');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_addresses');
    }
}
