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
        Schema::table('ngn_mot_notifier', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['motorbike_id']);

            // Change the column to be nullable
            $table->unsignedBigInteger('motorbike_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ngn_mot_notifier', function (Blueprint $table) {
            // Re-add the foreign key constraint
            $table->foreign('motorbike_id')->references('id')->on('motorbikes');

            // Make the column required again
            $table->unsignedBigInteger('motorbike_id')->nullable(false)->change();
        });
    }
};
