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
        Schema::table('ngn_campaign_referrals', function (Blueprint $table) {
            $table->foreign(['ngn_campaign_id'])->references(['id'])->on('ngn_campaigns')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['referrer_club_member_id'])->references(['id'])->on('club_members')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ngn_campaign_referrals', function (Blueprint $table) {
            $table->dropForeign('ngn_campaign_referrals_ngn_campaign_id_foreign');
            $table->dropForeign('ngn_campaign_referrals_referrer_club_member_id_foreign');
        });
    }
};
