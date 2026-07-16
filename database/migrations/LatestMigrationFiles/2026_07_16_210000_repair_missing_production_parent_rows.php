<?php

use App\Support\ProductionRelationRepair;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/**
 * Re-insert missing parent rows so child tables match older production relations.
 * Non-destructive: insert-only for missing parents; never updates or deletes business rows.
 */
return new class extends Migration
{
    public function up(): void
    {
        $result = ProductionRelationRepair::repairMissingParents();

        Log::info('production_relation_repair.missing_parents', $result);
    }

    public function down(): void
    {
        // Data repair is not reversed automatically.
    }
};
