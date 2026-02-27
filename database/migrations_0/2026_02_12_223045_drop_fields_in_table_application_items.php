<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('application_items', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'due_date', 'end_date', 'weekly_instalment']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_items', function (Blueprint $table) {
            $table->date('start_date')->default(DB::raw('CURRENT_DATE'));
            $table->date('due_date')->nullable();
            $table->date('end_date')->nullable(true);
            $table->decimal('weekly_instalment', 10, 2)->default(0.00);
        });
    }
};
