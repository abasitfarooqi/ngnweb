<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('renting_referral_point_ledger')) {
            return;
        }

        $indexes = collect(\Illuminate\Support\Facades\DB::select('SHOW INDEX FROM renting_referral_point_ledger'))
            ->pluck('Key_name')
            ->unique()
            ->all();

        if (! in_array('renting_referral_ledger_referral_dir_uidx', $indexes, true)) {
            Schema::table('renting_referral_point_ledger', function (Blueprint $table): void {
                $table->unique(['referral_id', 'direction'], 'renting_referral_ledger_referral_dir_uidx');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('renting_referral_point_ledger')) {
            return;
        }

        $indexes = collect(\Illuminate\Support\Facades\DB::select('SHOW INDEX FROM renting_referral_point_ledger'))
            ->pluck('Key_name')
            ->unique()
            ->all();

        if (in_array('renting_referral_ledger_referral_dir_uidx', $indexes, true)) {
            Schema::table('renting_referral_point_ledger', function (Blueprint $table): void {
                $table->dropUnique('renting_referral_ledger_referral_dir_uidx');
            });
        }
    }
};
