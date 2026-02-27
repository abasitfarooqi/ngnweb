<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('stock_logs', 'color')) {
            return;
        }
        Schema::table('stock_logs', function (Blueprint $table) {
            $table->string('color')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('stock_logs', 'color')) {
            return;
        }
        Schema::table('stock_logs', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
