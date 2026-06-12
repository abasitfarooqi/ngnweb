<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('finance_applications', 'is_new')) {
            return;
        }

        Schema::table('finance_applications', function (Blueprint $table) {
            $table->boolean('is_new')->default(false)->after('sold_by');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('finance_applications', 'is_new')) {
            return;
        }

        Schema::table('finance_applications', function (Blueprint $table) {
            $table->dropColumn('is_new');
        });
    }
};
