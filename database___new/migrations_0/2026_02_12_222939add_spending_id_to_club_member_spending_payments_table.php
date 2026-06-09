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
        Schema::table('club_member_spending_payments', function (Blueprint $table) {
            // Add spending_id foreign key
            $table->unsignedBigInteger('spending_id')->nullable()->after('club_member_id');
            $table->foreign('spending_id')->references('id')->on('club_member_spendings')->onDelete('restrict');
        });
        
        // Rename payment_amount to received_total using raw SQL
        \DB::statement('ALTER TABLE `club_member_spending_payments` CHANGE `payment_amount` `received_total` DECIMAL(10,2) NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('club_member_spending_payments', function (Blueprint $table) {
            // Drop foreign key and column
            $table->dropForeign(['spending_id']);
            $table->dropColumn('spending_id');
        });
        
        // Rename back using raw SQL
        \DB::statement('ALTER TABLE `club_member_spending_payments` CHANGE `received_total` `payment_amount` DECIMAL(10,2) NOT NULL');
    }
};
