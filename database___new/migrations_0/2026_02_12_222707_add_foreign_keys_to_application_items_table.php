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
            $table->foreign(['application_id'])->references(['id'])->on('finance_applications')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['motorbike_id'])->references(['id'])->on('motorbikes')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_items', function (Blueprint $table) {
            $table->dropForeign('application_items_application_id_foreign');
            $table->dropForeign('application_items_motorbike_id_foreign');
            $table->dropForeign('application_items_user_id_foreign');
        });
    }
};
