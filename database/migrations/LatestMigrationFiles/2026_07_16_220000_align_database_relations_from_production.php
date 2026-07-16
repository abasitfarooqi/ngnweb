<?php

use App\Support\ProductionDatabaseRelationAligner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/**
 * Align connected DB foreign keys to older production (SYNC_PROD_DB_* / nqfkhvtysa).
 * 1) Insert missing parent rows (from sync_prod, never delete/update business data)
 * 2) Add any FK constraints present on production but missing on connected DB
 *
 * Safe to re-run: skips existing constraints and already-valid references.
 */
return new class extends Migration
{
    public function up(): void
    {
        $compareBefore = ProductionDatabaseRelationAligner::compareConnectedToProduction();

        $result = ProductionDatabaseRelationAligner::align();

        $compareAfter = ProductionDatabaseRelationAligner::compareConnectedToProduction();

        Log::info('production_database_relation_align', [
            'before' => $compareBefore,
            'result' => $result,
            'after' => $compareAfter,
        ]);

        if ($compareAfter['orphan_constraints'] !== []) {
            Log::warning('production_database_relation_align.orphans_remain', [
                'orphans' => $compareAfter['orphan_constraints'],
            ]);
        }
    }

    public function down(): void
    {
        // Alignment is not reversed automatically.
    }
};
