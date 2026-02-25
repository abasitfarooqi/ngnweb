<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('club_member_redeem', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_member_id')->constrained()->onDelete('restrict');
            $table->datetime('date')->default(now());
            $table->decimal('redeem_total', 10, 2);
            $table->unsignedBigInteger('club_member_purchases_id_from');
            $table->foreign('club_member_purchases_id_from')->references('id')->on('club_member_purchases')->onDelete('restrict');
            $table->unsignedBigInteger('club_member_purchases_id_to');
            $table->foreign('club_member_purchases_id_to')->references('id')->on('club_member_purchases')->onDelete('restrict');
            $table->string('note')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_member_redeem');
    }
};
