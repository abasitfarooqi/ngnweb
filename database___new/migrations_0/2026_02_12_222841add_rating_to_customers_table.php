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
        if (Schema::hasColumn('customers', 'rating')) {
            return;
        }
        Schema::table('customers', function (Blueprint $table) {
            $table->integer('rating')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('customers', 'rating')) {
            return;
        }
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('rating');
        });
    }
};
