<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('club_member_redeem', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['club_member_purchases_id_from']);
            $table->dropForeign(['club_member_purchases_id_to']);

            // Now drop the columns
            $table->dropColumn('club_member_purchases_id_from');
            $table->dropColumn('club_member_purchases_id_to');
        });
    }

    public function down(): void
    {
        Schema::table('club_member_redeem', function (Blueprint $table) {});
    }
};
