<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Column name contains spaces + dot, so use raw SQL with proper quoting.
        if (!Schema::hasColumn('customers', 'WHATSAPP NO.')) {
            DB::statement("ALTER TABLE `customers` ADD COLUMN `WHATSAPP NO.` VARCHAR(50) NULL AFTER `updated`");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('customers', 'WHATSAPP NO.')) {
            DB::statement("ALTER TABLE `customers` DROP COLUMN `WHATSAPP NO.`");
        }
    }
};
