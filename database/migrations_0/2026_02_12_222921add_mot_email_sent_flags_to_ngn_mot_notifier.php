<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('ngn_mot_notifier', function (Blueprint $table) {
            $table->boolean('mot_email_sent_30')->default(false)->after('mot_is_notified_30');
            $table->boolean('mot_email_sent_10')->default(false)->after('mot_is_notified_10');
            $table->boolean('mot_email_sent_expired')->default(false)->after('mot_is_email_sent');
        });
    }

    public function down()
    {
        Schema::table('ngn_mot_notifier', function (Blueprint $table) {
            $table->dropColumn(['mot_email_sent_30', 'mot_email_sent_10', 'mot_email_sent_expired']);
        });
    }
};
