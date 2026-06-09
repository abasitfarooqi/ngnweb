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
        Schema::table('discountables', function (Blueprint $table) {
            $table->foreign(['discount_id'])->references(['id'])->on('discounts')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discountables', function (Blueprint $table) {
            $table->dropForeign('discountables_discount_id_foreign');
        });
    }
};
