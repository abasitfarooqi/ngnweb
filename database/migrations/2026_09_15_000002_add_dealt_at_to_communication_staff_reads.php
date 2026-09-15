<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('communication_staff_reads', function (Blueprint $table): void {
            $table->timestamp('dealt_at')->nullable()->after('opened_at');
        });
    }

    public function down(): void
    {
        Schema::table('communication_staff_reads', function (Blueprint $table): void {
            $table->dropColumn('dealt_at');
        });
    }
};
