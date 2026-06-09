<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('agreement_accesses', 'booking_id')) {
            return;
        }
        Schema::table('agreement_accesses', function (Blueprint $table) {
            $table->unsignedBigInteger('booking_id')->nullable(false);
            $table->foreign('booking_id')->references('id')->on('renting_bookings')->onDelete('restrict');
        });
    }

    public function down()
    {
        if (! Schema::hasColumn('agreement_accesses', 'booking_id')) {
            return;
        }
        Schema::table('agreement_accesses', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropColumn('booking_id');
        });
    }
};
