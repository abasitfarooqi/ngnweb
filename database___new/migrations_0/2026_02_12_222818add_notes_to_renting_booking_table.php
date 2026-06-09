<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('renting_bookings', 'notes')) {
            return;
        }
        Schema::table('renting_bookings', function (Blueprint $table) {
            $table->text('notes')->nullable();
        });
    }

    public function down()
    {
        if (! Schema::hasColumn('renting_bookings', 'notes')) {
            return;
        }
        Schema::table('renting_bookings', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
