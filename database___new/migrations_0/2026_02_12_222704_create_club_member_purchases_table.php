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
        Schema::create('club_member_purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('date')->default('2024-09-30 14:56:56');
            $table->unsignedBigInteger('club_member_id')->index('club_member_purchases_club_member_id_foreign');
            $table->decimal('percent', 5);
            $table->decimal('total', 10);
            $table->decimal('discount', 10);
            $table->boolean('is_redeemed')->default(false);
            $table->unsignedBigInteger('user_id')->index('club_member_purchases_user_id_foreign');
            $table->timestamps();
            $table->string('pos_invoice')->nullable()->unique();
            $table->string('branch_id')->nullable();
            $table->decimal('redeem_amount', 8, 4)->nullable();
            $table->decimal('price', 8, 4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_member_purchases');
    }
};
