<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('club_members', 'passkey')) {
            return;
        }
        Schema::table('club_members', function (Blueprint $table) {
            $table->string('passkey', 10)->nullable()->after('tc_agreed');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('club_members', 'passkey')) {
            return;
        }
        Schema::table('club_members', function (Blueprint $table) {
            $table->dropColumn('passkey');
        });
    }
};
