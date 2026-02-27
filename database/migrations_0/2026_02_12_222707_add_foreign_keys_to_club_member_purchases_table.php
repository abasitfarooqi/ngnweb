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
        Schema::table('club_member_purchases', function (Blueprint $table) {
            $table->foreign(['club_member_id'])->references(['id'])->on('club_members')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('club_member_purchases', function (Blueprint $table) {
            $table->dropForeign('club_member_purchases_club_member_id_foreign');
            $table->dropForeign('club_member_purchases_user_id_foreign');
        });
    }
};
