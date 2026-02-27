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
        Schema::table('ds_order_items', function (Blueprint $table) {
            $table->decimal('distance', 10, 2)->nullable()->after('dropoff_address')->comment('Total approx. Distance in miles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ds_order_items', function (Blueprint $table) {
            $table->dropColumn('distance');
        });
    }
};
