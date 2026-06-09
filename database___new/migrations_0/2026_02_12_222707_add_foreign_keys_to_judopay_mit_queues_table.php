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
        Schema::table('judopay_mit_queues', function (Blueprint $table) {
            $table->foreign(['authorized_by'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ngn_mit_queue_id'])->references(['id'])->on('ngn_mit_queues')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('judopay_mit_queues', function (Blueprint $table) {
            $table->dropForeign('judopay_mit_queues_authorized_by_foreign');
            $table->dropForeign('judopay_mit_queues_ngn_mit_queue_id_foreign');
        });
    }
};
