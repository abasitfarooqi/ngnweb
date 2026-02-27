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
        if (Schema::hasColumn('finance_applications', 'extra_items')) {
            return;
        }
        Schema::table('finance_applications', function (Blueprint $table) {
            // add new field text extra
            $table->text('extra_items')->nullable();
            // extra field decimal
            $table->decimal('extra', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('finance_applications', 'extra_items')) {
            return;
        }
        Schema::table('finance_applications', function (Blueprint $table) {
            $table->dropColumn('extra_items');
            $table->dropColumn('extra');
        });
    }
};
