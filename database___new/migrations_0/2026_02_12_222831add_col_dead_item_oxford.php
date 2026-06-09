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
        if (Schema::hasColumn('oxford_products', 'dead')) {
            return;
        }
        Schema::table('oxford_products', function (Blueprint $table) {
            $table->boolean('dead')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('oxford_products', 'dead')) {
            return;
        }
        Schema::table('oxford_products', function (Blueprint $table) {
            $table->dropColumn('dead');
        });
    }
};
