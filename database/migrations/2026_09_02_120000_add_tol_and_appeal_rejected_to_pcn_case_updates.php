<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pcn_case_updates', function (Blueprint $table) {
            if (! Schema::hasColumn('pcn_case_updates', 'is_tol_requested')) {
                $table->boolean('is_tol_requested')->default(false)->after('is_appealed');
            }

            if (! Schema::hasColumn('pcn_case_updates', 'is_appeal_rejected')) {
                $table->boolean('is_appeal_rejected')->default(false)->after('is_tol_requested');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pcn_case_updates', function (Blueprint $table) {
            $table->dropColumn(['is_tol_requested', 'is_appeal_rejected']);
        });
    }
};
