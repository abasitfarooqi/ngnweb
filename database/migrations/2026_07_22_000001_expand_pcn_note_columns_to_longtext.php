<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE `pcn_cases` MODIFY `note` LONGTEXT NULL');
        DB::statement('ALTER TABLE `pcn_case_updates` MODIFY `note` LONGTEXT NOT NULL');
        DB::statement('ALTER TABLE `pcn_tol_requests` MODIFY `note` LONGTEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `pcn_cases` MODIFY `note` TEXT NULL');
        DB::statement('ALTER TABLE `pcn_case_updates` MODIFY `note` TEXT NOT NULL');
        DB::statement('ALTER TABLE `pcn_tol_requests` MODIFY `note` TEXT NULL');
    }
};
