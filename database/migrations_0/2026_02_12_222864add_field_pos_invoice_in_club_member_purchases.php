<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('club_member_purchases', 'pos_invoice')) {
            return;
        }
        Schema::table('club_member_purchases', function (Blueprint $table) {
            $table->string('pos_invoice')->nullable();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('club_member_purchases', 'pos_invoice')) {
            return;
        }
        Schema::table('club_member_purchases', function (Blueprint $table) {
            $table->dropColumn('pos_invoice');
        });
    }
};
