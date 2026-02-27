<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ngn_categories', function (Blueprint $table) {
            $table->foreignId('super_category_id')->nullable()->constrained('ngn_super_categories')->onDelete('restrict');
        });

    }

    public function down(): void
    {
        Schema::table('ngn_categories', function (Blueprint $table) {
            $table->dropForeign(['super_category_id']);
            $table->dropColumn('super_category_id');
        });
    }
};
