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
            $table->id();
            $table->datetime('date')->default(now());
            $table->unsignedBigInteger('club_member_id');
            $table->foreign('club_member_id')->references('id')->on('club_members')->onDelete('restrict');
            $table->decimal('total', 10, 2);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->string('pos_invoice')->nullable();
            $table->string('branch_id')->nullable();
            $table->timestamps();
            
            $table->unique('pos_invoice');
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
