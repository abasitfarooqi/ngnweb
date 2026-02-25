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
            $table->id();
            $table->foreignId('club_member_id')->constrained()->onDelete('restrict');
            $table->datetime('date')->default(now());
            $table->decimal('payment_amount', 10, 2);
            $table->string('pos_invoice')->nullable();
            $table->unsignedBigInteger('user_id');
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
