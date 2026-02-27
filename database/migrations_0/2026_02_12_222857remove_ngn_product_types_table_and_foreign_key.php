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
        // // First, remove the foreign key constraint from the `ngn_products` table
        // Schema::table('ngn_products', function (Blueprint $table) {
        //     $table->dropForeign(['product_type_id']);
        // });

        // // Next, remove the `product_type_id` column from the `ngn_products` table
        // Schema::table('ngn_products', function (Blueprint $table) {
        //     $table->dropColumn('product_type_id');
        // });

        // // Finally, drop the `ngn_product_types` table
        // Schema::dropIfExists('ngn_product_types');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('ngn_products');
        Schema::dropIfExists('ngn_product_types');

    }
};
