<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stock_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('stock_logs', 'picture')) {
                $table->text('picture')->nullable()->after('color');
            } else {
                $table->text('picture')->nullable()->change();
            }
        });
    }

    public function down()
    {
        Schema::table('stock_logs', function (Blueprint $table) {
            $table->dropColumn('picture');
        });
    }
};
