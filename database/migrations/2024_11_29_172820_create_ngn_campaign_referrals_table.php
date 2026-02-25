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
            $table->id();
            $table->foreignId('ngn_campaign_id')->constrained('ngn_campaigns')->onDelete('restrict');
            $table->foreignId('referrer_club_member_id')->constrained('club_members')->onDelete('restrict');
            $table->string('referred_full_name')->nullable(false);
            $table->string('referred_phone')->nullable(false);
            $table->string('referred_reg_number')->nullable(true);
            $table->string('referral_code')->nullable(false);
            $table->boolean('validated')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngn_campaig_referral');
    }
};
