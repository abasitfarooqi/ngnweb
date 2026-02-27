<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('club_member_purchases', function (Blueprint $table) {
            $table->decimal('redeem_amount', 8, 4)->nullable();
            $table->decimal('price', 8, 4)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('club_member_purchases', function (Blueprint $table) {
            $table->dropColumn('redeem_amount');
        });
    }
};
