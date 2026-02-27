<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('motorbikes_cat_b', function (Blueprint $table) {
            $table->unique('motorbike_id');
        });
    }

    public function down()
    {
        Schema::table('motorbikes_cat_b', function (Blueprint $table) {
            $table->dropUnique('motorbike_id');
        });
    }
};
