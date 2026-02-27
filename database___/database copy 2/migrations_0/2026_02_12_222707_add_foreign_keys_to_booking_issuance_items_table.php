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
        Schema::table('booking_issuance_items', function (Blueprint $table) {
            $table->foreign(['booking_item_id'])->references(['id'])->on('renting_booking_items')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['issued_by_user_id'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_issuance_items', function (Blueprint $table) {
            $table->dropForeign('booking_issuance_items_booking_item_id_foreign');
            $table->dropForeign('booking_issuance_items_issued_by_user_id_foreign');
        });
    }
};
