<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop all tables if they exist
        Schema::dropIfExists('ngn_pos_inventory');
        Schema::dropIfExists('ngn_products');
        Schema::dropIfExists('ngn_models');
        Schema::dropIfExists('ngn_categories');
        // ngn_brands table handled by separate clean migration
        Schema::dropIfExists('ngn_stock');
        Schema::dropIfExists('ngn_branch');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop all tables if they exist (this is the same in reverse, since you're only deleting)
        DB::transaction(function () {
            Schema::dropIfExists('ngn_pos_inventory');
            Schema::dropIfExists('ngn_products');
            Schema::dropIfExists('ngn_stock');
            Schema::dropIfExists('ngn_branch');
            Schema::dropIfExists('ngn_models');
            Schema::dropIfExists('ngn_categories');
            // ngn_brands table handled by separate clean migration

        });
    }
};
