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
        Schema::table('ec_order_items', function (Blueprint $table) {
            $table->string('item_type')->default('catalogue')->after('product_id');
            $table->string('part_number')->nullable()->after('sku');
            $table->unsignedBigInteger('sp_part_id')->nullable()->after('part_number');
            $table->unsignedBigInteger('sp_assembly_id')->nullable()->after('sp_part_id');
            $table->json('source_meta')->nullable()->after('line_total');

            $table->index('item_type');
            $table->index('part_number');
            $table->index('sp_part_id');
            $table->index('sp_assembly_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ec_order_items', function (Blueprint $table) {
            $table->dropIndex(['item_type']);
            $table->dropIndex(['part_number']);
            $table->dropIndex(['sp_part_id']);
            $table->dropIndex(['sp_assembly_id']);

            $table->dropColumn([
                'item_type',
                'part_number',
                'sp_part_id',
                'sp_assembly_id',
                'source_meta',
            ]);
        });
    }
};
