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
            $table->string('slug')->default('');
            $table->text('description')->nullable();
            $table->boolean('is_ecommerce')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('meta_title')->default('');
            $table->text('meta_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ngn_categories', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('description');
            $table->dropColumn('is_ecommerce');
            $table->dropColumn('is_active');
            $table->dropColumn('sort_order');
            $table->dropColumn('meta_title');
            $table->dropColumn('meta_description');
        });
    }
};
