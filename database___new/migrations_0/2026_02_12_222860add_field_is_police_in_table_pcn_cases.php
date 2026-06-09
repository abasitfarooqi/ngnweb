<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('pcn_cases', 'is_police')) {
            return;
        }
        Schema::table('pcn_cases', function (Blueprint $table) {
            $table->boolean('is_police')->default(false);
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('pcn_cases', 'is_police')) {
            return;
        }
        Schema::table('pcn_cases', function (Blueprint $table) {
            $table->dropColumn('is_police');
        });
    }
};
