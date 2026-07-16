<?php

use App\Support\ProductionForeignKeyRestorer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/**
 * Restore production foreign keys on existing tables without touching row data.
 * Skips constraints that still have orphan references (run parent repair first).
 */
return new class extends Migration
{
    public function up(): void
    {
        $result = ProductionForeignKeyRestorer::restoreMissing();

        Log::info('production_foreign_key_restore', $result);

        if ($result['failed'] !== []) {
            $messages = array_map(
                static fn (array $row): string => $row['name'].': '.$row['message'],
                $result['failed']
            );

            throw new RuntimeException(
                'Some foreign keys could not be restored: '.implode(' | ', $messages)
            );
        }
    }

    public function down(): void
    {
        // FK restore is not reversed automatically.
    }
};
