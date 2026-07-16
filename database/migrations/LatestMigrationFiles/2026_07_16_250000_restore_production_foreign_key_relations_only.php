<?php

use App\Support\ProductionDatabaseRelationAligner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/**
 * Restore production foreign-key relations only (older Cloudways ERD).
 * Adds/realigns CONSTRAINT definitions — no INSERT/UPDATE/DELETE on business table rows.
 */
return new class extends Migration
{
    public function up(): void
    {
        $before = ProductionDatabaseRelationAligner::compareConnectedToProduction();

        $result = ProductionDatabaseRelationAligner::alignRelationsOnly();

        $after = ProductionDatabaseRelationAligner::compareConnectedToProduction();

        Log::info('production_foreign_key_relations_only', [
            'before' => $before,
            'result' => $result,
            'after' => $after,
        ]);
    }

    public function down(): void
    {
        // Not reversed automatically.
    }
};
