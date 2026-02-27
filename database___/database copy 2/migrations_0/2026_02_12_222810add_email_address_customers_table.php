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
        Schema::table('customers', function (Blueprint $table) {
            if (! Schema::hasColumn('customers', 'nationality')) {
                $table->string('nationality')->nullable();
            }
            if (! Schema::hasColumn('customers', 'reputation_note')) {
                $table->text('reputation_note')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'nationality')) {
                $table->dropColumn('nationality');
            }
            if (Schema::hasColumn('customers', 'reputation_note')) {
                $table->dropColumn('reputation_note');
            }
        });
    }
};
