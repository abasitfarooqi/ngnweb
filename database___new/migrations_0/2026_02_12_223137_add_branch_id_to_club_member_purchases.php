<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('club_member_purchases', function (Blueprint $table) {
            $table->string('branch_id')->nullable()->after('pos_invoice');
            $table->string('pos_invoice')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('club_member_purchases', function (Blueprint $table) {
            $table->dropColumn('branch_id');
            $table->string('pos_invoice')->change();
        });
    }
};
