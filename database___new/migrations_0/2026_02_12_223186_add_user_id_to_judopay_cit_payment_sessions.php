<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('judopay_cit_payment_sessions', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('subscription_id')
                ->constrained()
                ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::table('judopay_cit_payment_sessions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
