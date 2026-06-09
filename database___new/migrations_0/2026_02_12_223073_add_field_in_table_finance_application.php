<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finance_applications', function (Blueprint $table) {
            $table->boolean('is_cancelled')->default(false);
            $table->dateTime('logbook_transfer_date')->nullable();
            $table->dateTime('cancelled_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('finance_applications', function (Blueprint $table) {
            $table->dropColumn('is_cancelled');
            $table->dropColumn('logbook_transfer_date');
            $table->dropColumn('cancelled_at');
        });
    }
};
