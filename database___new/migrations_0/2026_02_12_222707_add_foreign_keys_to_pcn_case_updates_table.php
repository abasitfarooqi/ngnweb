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
        Schema::table('pcn_case_updates', function (Blueprint $table) {
            $table->foreign(['case_id'])->references(['id'])->on('pcn_cases')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pcn_case_updates', function (Blueprint $table) {
            $table->dropForeign('pcn_case_updates_case_id_foreign');
            $table->dropForeign('pcn_case_updates_user_id_foreign');
        });
    }
};
