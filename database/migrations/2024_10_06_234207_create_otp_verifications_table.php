<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('club_member_id');
            $table->string('otp_code', 255);
            $table->timestamp('expires_at');
            $table->boolean('is_used')->default(false);
            $table->timestamps();
            $table->foreign('club_member_id')->references('id')->on('club_members')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('otp_verifications');
    }
};
