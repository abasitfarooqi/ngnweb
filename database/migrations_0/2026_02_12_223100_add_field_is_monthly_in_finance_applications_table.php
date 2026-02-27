<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finance_applications', function (Blueprint $table) {
            $table->boolean('is_monthly')->default(false)->after('weekly_instalment');
        });
    }

    public function down(): void
    {
        Schema::table('finance_applications', function (Blueprint $table) {
            $table->dropColumn('is_monthly');
        });
    }
};
