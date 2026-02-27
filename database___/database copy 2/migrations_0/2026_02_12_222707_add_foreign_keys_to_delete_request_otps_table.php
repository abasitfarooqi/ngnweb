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
        Schema::table('delete_request_otps', function (Blueprint $table) {
            $table->foreign(['purchase_id'])->references(['id'])->on('club_member_purchases')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delete_request_otps', function (Blueprint $table) {
            $table->dropForeign('delete_request_otps_purchase_id_foreign');
        });
    }
};
