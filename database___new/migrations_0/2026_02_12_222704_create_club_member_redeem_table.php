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
        Schema::create('club_member_redeem', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('club_member_id')->index('club_member_redeem_club_member_id_foreign');
            $table->dateTime('date')->default('2024-09-30 14:56:56');
            $table->decimal('redeem_total', 10);
            $table->string('note')->nullable();
            $table->unsignedBigInteger('user_id')->index('club_member_redeem_user_id_foreign');
            $table->timestamps();
            $table->string('pos_invoice')->nullable();
            $table->string('branch_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_member_redeem');
    }
};
