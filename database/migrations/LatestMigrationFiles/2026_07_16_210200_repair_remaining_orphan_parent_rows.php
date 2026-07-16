<?php

use App\Support\ProductionRelationRepair;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/**
 * Idempotent follow-up: stub parents that no longer exist on older production (e.g. country_id=81).
 */
return new class extends Migration
{
    public function up(): void
    {
        $result = ProductionRelationRepair::repairMissingParents();

        Log::info('production_relation_repair.follow_up', $result);
    }

    public function down(): void
    {
        // Not reversed automatically.
    }
};
