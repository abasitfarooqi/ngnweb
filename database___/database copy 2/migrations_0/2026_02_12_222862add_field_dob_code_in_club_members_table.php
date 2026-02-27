<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('club_members', 'dob_code')) {
            return;
        }
        Schema::table('club_members', function (Blueprint $table) {
            $table->date('dob_code')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('club_members', 'dob_code')) {
            return;
        }
        Schema::table('club_members', function (Blueprint $table) {
            $table->dropColumn('dob_code');
        });
    }
};
