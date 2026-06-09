<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finance_applications', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'due_date', 'state']);
            $table->date('contract_date')->nullable();
            $table->date('first_instalment_date')->nullable();
            $table->decimal('weekly_instalment', 10, 2)->default(0.00);
        });
    }

    public function down(): void
    {
        Schema::table('finance_applications', function (Blueprint $table) {
            $table->date('start_date')->default(DB::raw('CURRENT_DATE'))->nullable();
            $table->date('due_date')->nullable();
            $table->string('state')->default('DRAFT');
            $table->dropColumn(['contract_date', 'first_instalment_date', 'weekly_instalment']);
        });
    }
};
