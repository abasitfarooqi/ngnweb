<?php

use App\Support\ProductionDatabaseRelationAligner;
use App\Support\ProductionForeignKeyRestorer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/**
 * Realign every shared FK to match older production exactly (constraint name + ON DELETE/UPDATE).
 * Fixes ERD differences caused by bootstrap using NO ACTION whilst production uses RESTRICT.
 * Does not touch row data. New-table FKs not on production are left unchanged.
 */
return new class extends Migration
{
    public function up(): void
    {
        $before = count(ProductionForeignKeyRestorer::ruleMismatchReport());

        $result = ProductionForeignKeyRestorer::realignWithProduction();

        Log::info('production_foreign_key_realign', [
            'mismatches_before' => $before,
            'result' => $result,
            'mismatches_after' => count(ProductionForeignKeyRestorer::ruleMismatchReport()),
        ]);

        if ($result['failed'] !== []) {
            $messages = array_map(
                static fn (array $row): string => $row['name'].': '.$row['message'],
                $result['failed']
            );

            throw new RuntimeException(
                'Foreign key realignment failed: '.implode(' | ', $messages)
            );
        }
    }

    public function down(): void
    {
        // Not reversed automatically.
    }
};
