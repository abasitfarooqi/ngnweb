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
        Schema::table('motorbikes_sold', function (Blueprint $table) {
            $table->foreign(['listing_id'])->references(['id'])->on('motorbikes_sale')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('motorbikes_sold', function (Blueprint $table) {
            $table->dropForeign('motorbikes_sold_listing_id_foreign');
        });
    }
};
