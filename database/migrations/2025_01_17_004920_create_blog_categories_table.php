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
        Schema::table('blog_categories', function (Blueprint $table) {
            // Add new columns
            $table->string('name')->nullable(false)->after('id');
            $table->string('slug')->unique()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('blog_categories', function (Blueprint $table) {
            // Drop the columns if rolling back
            $table->dropColumn(['name', 'slug']);
        });
    }
};
