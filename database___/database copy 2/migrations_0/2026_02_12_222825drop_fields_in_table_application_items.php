<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $cols = ['start_date', 'due_date', 'end_date', 'weekly_instalment'];
        $drop = array_filter($cols, fn ($c) => Schema::hasColumn('application_items', $c));
        if (empty($drop)) {
            return;
        }
        Schema::table('application_items', function (Blueprint $table) use ($drop) {
            $table->dropColumn($drop);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('application_items', 'start_date')) {
            return;
        }
        Schema::table('application_items', function (Blueprint $table) {
            $table->date('start_date')->default(DB::raw('CURRENT_DATE'));
            $table->date('due_date')->nullable();
            $table->date('end_date')->nullable(true);
            $table->decimal('weekly_instalment', 10, 2)->default(0.00);
        });
    }
};
