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
        Schema::create('judopay_mit_queues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ngn_mit_queue_id')->index('judopay_mit_queues_ngn_mit_queue_id_foreign');
            $table->string('judopay_payment_reference')->nullable()->unique()->comment('yourPaymentReference for this MIT run. The procedure will generate the payment reference. and same will write here and prepare for the payload.');
            $table->boolean('cleared')->default(false)->comment('Whether this specific MIT attempt was successful/cleared');
            $table->dateTime('cleared_at')->nullable()->comment('When this specific MIT attempt was cleared/succeeded');
            $table->dateTime('mit_fire_date')->comment('MIT fire date. When is the MIT fire date.');
            $table->integer('retry')->default(0)->comment('ONLY IF API NOT RESPONSE. HTTP ERROR OR TIMEOUT WHICH WILL RETRY IN NEXT 30 Seconds. The Fire means if JudoPay API response http is respoinse.');
            $table->boolean('fired')->default(false)->comment('If MIT has been fired. Only if Judopay acknowledge the request is made.');
            $table->unsignedBigInteger('authorized_by')->index('judopay_mit_queues_authorized_by_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('judopay_mit_queues');
    }
};
