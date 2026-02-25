<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Idempotency key. It is self contained. it is a firing chamber. It must fire if record exists and date is not passed.
        Schema::create('judopay_mit_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ngn_mit_queue_id')->constrained('ngn_mit_queues')->restrictOnDelete();

            $table->string('judopay_payment_reference')->unique()->nullable()
                ->comment('yourPaymentReference for this MIT run. The procedure will generate the payment reference. and same will write here and prepare for the payload.');
            
            $table->boolean('cleared')->default(false)
                ->comment('Whether this specific MIT attempt was successful/cleared');
            
            $table->datetime('cleared_at')->nullable()
                ->comment('When this specific MIT attempt was cleared/succeeded');

                
            $table->datetime('mit_fire_date')->comment('MIT fire date. When is the MIT fire date.');
            $table->integer('retry')->default(0)->comment('ONLY IF API NOT RESPONSE. HTTP ERROR OR TIMEOUT WHICH WILL RETRY IN NEXT 30 Seconds. The Fire means if JudoPay API response http is respoinse.');
            $table->boolean('fired')->default(false)->comment('If MIT has been fired. Only if Judopay acknowledge the request is made.');

            $table->foreignId('authorized_by')->constrained('users')->restrictOnDelete()->comment('User who authorized the MIT.');

            $table->timestamps();
        });
    }
};
