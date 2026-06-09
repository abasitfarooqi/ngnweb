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
        Schema::create('club_member_spendings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('date')->default('2025-12-18 17:09:53');
            $table->unsignedBigInteger('club_member_id')->index('club_member_spendings_club_member_id_foreign');
            $table->decimal('total', 10);
            $table->decimal('paid_amount', 10)->default(0);
            $table->boolean('is_paid')->default(false);
            $table->unsignedBigInteger('user_id')->index('club_member_spendings_user_id_foreign');
            $table->string('pos_invoice')->nullable()->unique();
            $table->string('branch_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_member_spendings');
    }
};
