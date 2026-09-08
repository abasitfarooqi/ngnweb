<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('renting_weekly_updates', 'charge_id')) {
            Schema::table('renting_weekly_updates', function (Blueprint $table): void {
                $table->foreignId('charge_id')->nullable()->after('invoice_id')->constrained('renting_other_charges')->nullOnDelete();
                $table->index(['charge_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('renting_weekly_updates', 'charge_id')) {
            Schema::table('renting_weekly_updates', function (Blueprint $table): void {
                $table->dropForeign(['charge_id']);
                $table->dropIndex(['charge_id', 'created_at']);
                $table->dropColumn('charge_id');
            });
        }
    }
};
