<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finance_applications', function (Blueprint $table) {
            $table->boolean('is_new_latest')->default(false)->after('is_used_extended_custom');
            $table->boolean('is_used_latest')->default(false)->after('is_new_latest');
        });
    }

    public function down(): void
    {
        Schema::table('finance_applications', function (Blueprint $table) {
            $table->dropColumn(['is_new_latest', 'is_used_latest']);
        });
    }
};
