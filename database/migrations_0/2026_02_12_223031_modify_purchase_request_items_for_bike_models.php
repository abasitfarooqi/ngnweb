<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchase_request_items', function (Blueprint $table) {

            $table->dropColumn('model');

            $table->unsignedBigInteger('bike_model_id')->after('brand_name_id');
            $table->foreign('bike_model_id')->references('id')->on('bike_models');
        });
    }

    public function down()
    {
        Schema::table('purchase_request_items', function (Blueprint $table) {

            $table->dropForeign(['bike_model_id']);
            $table->dropColumn('bike_model_id');

            $table->string('model')->after('brand_name_id');
        });
    }
};
