<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Use raw SQL so we don't need doctrine/dbal for change()
        // Keeps existing type (assumes VARCHAR) and just allows NULL.
        DB::statement("ALTER TABLE `customers` MODIFY `username` VARCHAR(255) NULL");
    }

    public function down(): void
    {
        // Revert to NOT NULL (no default) - be careful: this can fail if nulls exist.
        DB::statement("ALTER TABLE `customers` MODIFY `username` VARCHAR(255) NOT NULL");
    }
};
