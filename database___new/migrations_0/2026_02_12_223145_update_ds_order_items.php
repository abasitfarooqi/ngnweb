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
            $table->text('documents')->nullable()->change();
            $table->text('keys')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ds_order_items', function (Blueprint $table) {
            $table->text('documents')->nullable(false)->change();
            $table->text('keys')->nullable(false)->change();
        });
    }
};
