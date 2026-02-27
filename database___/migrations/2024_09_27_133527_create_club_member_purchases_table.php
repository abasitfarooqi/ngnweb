<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('club_member_purchases', function (Blueprint $table) {
            $table->id();
            $table->datetime('date')->default(now());
            $table->unsignedBigInteger('club_member_id');
            $table->foreign('club_member_id')->references('id')->on('club_members')->onDelete('restrict');
            $table->decimal('percent', 5, 2);
            $table->decimal('total', 10, 2);
            $table->decimal('discount', 10, 2);
            $table->boolean('is_redeemed')->default(false);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_member_purchases');
    }
};
