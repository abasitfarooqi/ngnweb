<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $drop = array_filter(['start_date', 'due_date', 'state'], fn ($c) => Schema::hasColumn('finance_applications', $c));
        if (! empty($drop)) {
            Schema::table('finance_applications', function (Blueprint $table) use ($drop) {
                $table->dropColumn($drop);
            });
        }
        Schema::table('finance_applications', function (Blueprint $table) {
            if (! Schema::hasColumn('finance_applications', 'contract_date')) {
                $table->date('contract_date')->nullable();
            }
            if (! Schema::hasColumn('finance_applications', 'first_instalment_date')) {
                $table->date('first_instalment_date')->nullable();
            }
            if (! Schema::hasColumn('finance_applications', 'weekly_instalment')) {
                $table->decimal('weekly_instalment', 10, 2)->default(0.00);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('finance_applications', 'start_date')) {
            Schema::table('finance_applications', function (Blueprint $table) {
                $table->date('start_date')->default(DB::raw('CURRENT_DATE'))->nullable();
                $table->date('due_date')->nullable();
                $table->string('state')->default('DRAFT');
            });
        }
        $drop = array_filter(['contract_date', 'first_instalment_date', 'weekly_instalment'], fn ($c) => Schema::hasColumn('finance_applications', $c));
        if (! empty($drop)) {
            Schema::table('finance_applications', function (Blueprint $table) use ($drop) {
                $table->dropColumn($drop);
            });
        }
    }
};
