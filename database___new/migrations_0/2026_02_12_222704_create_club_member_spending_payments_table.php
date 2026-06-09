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
        Schema::create('club_member_spending_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('club_member_id')->index('club_member_spending_payments_club_member_id_foreign');
            $table->unsignedBigInteger('spending_id')->nullable()->index('club_member_spending_payments_spending_id_foreign');
            $table->dateTime('date')->default('2026-01-17 21:08:57');
            $table->decimal('received_total', 10);
            $table->string('pos_invoice')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('branch_id')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_member_spending_payments');
    }
};
