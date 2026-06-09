<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // // List of tables to reset
        // $tables = [
        //     'ngn_models',
        //     'ngn_categories',
        //     'ngn_brands',
        //     'ngn_products',
        // ];

        // // Temporarily disable foreign key checks
        // DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        // foreach ($tables as $table) {
        //     // Truncate the table to ensure it is empty
        //     DB::table($table)->truncate();

        //     // Reset the AUTO_INCREMENT value to 1
        //     DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = 1;");
        // }

        // // Re-enable foreign key checks
        // DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Typically, there's no meaningful way to revert a truncate operation
        // You might want to handle this based on your specific needs.
    }
};
