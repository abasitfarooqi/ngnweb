<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ngn_categories', function (Blueprint $table) {
            $table->foreign(['super_category_id'])->references(['id'])->on('ngn_super_categories')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ngn_categories', function (Blueprint $table) {
            $table->dropForeign('ngn_categories_super_category_id_foreign');
        });
    }
};
