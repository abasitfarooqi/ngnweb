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
        Schema::create('backup_club_member_purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date')->nullable();
            $table->unsignedBigInteger('club_member_id')->nullable();
            $table->decimal('percent', 8, 4)->nullable();
            $table->decimal('total', 8, 4)->nullable();
            $table->decimal('discount', 8, 4)->nullable();
            $table->boolean('is_redeemed')->default(false);
            $table->decimal('redeem_amount', 8, 4)->nullable();
            $table->string('pos_invoice')->nullable();
            $table->string('branch_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('original_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_club_member_purchases');
    }
};
