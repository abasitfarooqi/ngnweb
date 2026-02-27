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
        Schema::table('ngn_models', function (Blueprint $table) {
            $table->string('slug')->default('');
            $table->string('meta_title')->default('');
            $table->text('meta_description')->nullable();
            $table->boolean('is_ecommerce')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ngn_models', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('meta_title');
            $table->dropColumn('meta_description');
            $table->dropColumn('is_ecommerce');
        });
    }
};
