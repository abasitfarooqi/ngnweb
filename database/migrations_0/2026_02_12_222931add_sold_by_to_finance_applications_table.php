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
        Schema::table('finance_applications', function (Blueprint $table) {
            $table->string('sold_by')->nullable()->after('user_id')
                ->comment('Person who sold the bike; set once, do not modify');
        });
    }

    public function down(): void
    {
        Schema::table('finance_applications', function (Blueprint $table) {
            $table->dropColumn('sold_by');
        });
    }
};
