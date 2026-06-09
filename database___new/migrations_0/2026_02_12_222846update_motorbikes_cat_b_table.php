<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('motorbikes_cat_b', 'branch_id')) {
            return;
        }
        Schema::table('motorbikes_cat_b', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('motorbikes_cat_b', 'branch_id')) {
            return;
        }
        Schema::table('motorbikes_cat_b', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};
