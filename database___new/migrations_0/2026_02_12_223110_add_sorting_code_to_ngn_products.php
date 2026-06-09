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
        Schema::table('ngn_products', function (Blueprint $table) {
            $table->string('sorting_code')->nullable()->default('0')->after('dead');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ngn_products', function (Blueprint $table) {
            $table->dropColumn('sorting_code');
        });
    }
};
