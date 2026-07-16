<?php

use App\Support\ProductionForeignKeyRestorer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/**
 * Final pass: fix any remaining constraint name/signature drift vs production catalog.
 */
return new class extends Migration
{
    public function up(): void
    {
        $before = count(ProductionForeignKeyRestorer::ruleMismatchReport());

        $result = ProductionForeignKeyRestorer::realignWithProduction();

        Log::info('production_foreign_key_final_realign', [
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
                'Final foreign key realignment failed: '.implode(' | ', $messages)
            );
        }
    }

    public function down(): void
    {
        // Not reversed automatically.
    }
};
