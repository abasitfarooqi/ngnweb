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
        Schema::create('ngn_campaign_referrals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ngn_campaign_id')->index('ngn_campaign_referrals_ngn_campaign_id_foreign');
            $table->unsignedBigInteger('referrer_club_member_id')->index('ngn_campaign_referrals_referrer_club_member_id_foreign');
            $table->string('referred_full_name');
            $table->string('referred_phone');
            $table->string('referred_reg_number')->nullable();
            $table->string('referral_code');
            $table->boolean('validated')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngn_campaign_referrals');
    }
};
