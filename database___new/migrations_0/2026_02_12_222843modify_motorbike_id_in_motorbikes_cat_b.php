<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $exists = DB::selectOne(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = "UNIQUE"',
            [DB::getDatabaseName(), 'motorbikes_cat_b', 'motorbikes_cat_b_motorbike_id_unique']
        );
        if ($exists) {
            return;
        }
        Schema::table('motorbikes_cat_b', function (Blueprint $table) {
            $table->unique('motorbike_id');
        });
    }

    public function down()
    {
        $exists = DB::selectOne(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = "UNIQUE"',
            [DB::getDatabaseName(), 'motorbikes_cat_b', 'motorbikes_cat_b_motorbike_id_unique']
        );
        if (! $exists) {
            return;
        }
        Schema::table('motorbikes_cat_b', function (Blueprint $table) {
            $table->dropUnique('motorbike_id');
        });
    }
};
