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
        Schema::table('renting_other_charges_transactions', function (Blueprint $table) {
            $table->foreign(['charges_id'])->references(['id'])->on('renting_other_charges')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('renting_other_charges_transactions', function (Blueprint $table) {
            $table->dropForeign('renting_other_charges_transactions_charges_id_foreign');
            $table->dropForeign('renting_other_charges_transactions_user_id_foreign');
        });
    }
};
