<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_logs', function (Blueprint $table) {
            // Check if the 'branch_id' column doesn't exist, then add it
            if (! Schema::hasColumn('stock_logs', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->default(1)->after('id');
            }

            // Check if the 'user_id' column doesn't exist, then add it
            if (! Schema::hasColumn('stock_logs', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('branch_id');
            }

            // Check if the 'sku' column doesn't exist, then add it
            if (! Schema::hasColumn('stock_logs', 'sku')) {
                $table->string('sku')->nullable()->after('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_logs', function (Blueprint $table) {
            $table->dropColumn('branch_id');
            $table->dropColumn('user_id');
            $table->dropColumn('sku');
        });
    }
};
