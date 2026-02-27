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
        if (! Schema::hasTable('inventories')) {
            Schema::create('inventories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code');
                $table->text('description')->nullable();
                $table->string('email');
                $table->string('street_address');
                $table->string('street_address_plus')->nullable();
                $table->string('zipcode');
                $table->string('city');
                $table->string('phone_number')->nullable();
                $table->integer('priority');
                $table->float('latitude')->nullable();
                $table->float('longitude')->nullable();
                $table->boolean('is_default');
                $table->unsignedBigInteger('country_id')->nullable();
                $table->timestamps();

                $table->foreign('country_id')->references('id')->on('system_countries');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
