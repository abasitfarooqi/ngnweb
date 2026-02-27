<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('system_countries')) {
            Schema::create('system_countries', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code', 3)->unique();
                $table->string('phone_code', 5)->nullable();
                $table->string('currency', 3)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
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
        Schema::dropIfExists('system_countries');
    }
}
