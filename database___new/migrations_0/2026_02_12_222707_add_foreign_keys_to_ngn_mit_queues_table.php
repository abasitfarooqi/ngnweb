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
        Schema::table('ngn_mit_queues', function (Blueprint $table) {
            $table->foreign(['cleared_by'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['subscribable_id'])->references(['id'])->on('judopay_subscriptions')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ngn_mit_queues', function (Blueprint $table) {
            $table->dropForeign('ngn_mit_queues_cleared_by_foreign');
            $table->dropForeign('ngn_mit_queues_subscribable_id_foreign');
        });
    }
};
