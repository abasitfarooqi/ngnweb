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
        // Only subscription status active would be queueable. Each Invoice is only one time queueable.
        // If MIT fire and clearn is completing the MIT life cycle. If not it will be fire again on 
        // retry attempt. and if fails so the manual collection taking place. Note: it will be twice sent to 
        // judpay_mit_queues table. (ONLY TWO TIMES the ngn_mit_queues id will sent to judopay_mit_queues table.)
        Schema::create('ngn_mit_queues', function (Blueprint $table) {
            $table->id();
            // morph (Renting, Finance) 
            $table->foreignId('subscribable_id')->constrained('judopay_subscriptions')->restrictOnDelete();
            $table->string('invoice_number');
            $table->date('invoice_date')->comment('Invoice due date. When is the invoice amount is due.');
            $table->datetime('mit_fire_date')
                ->comment('MIT fire date. When is the MIT fire date.');

            $table->enum('mit_attempt', ['not attempt', 'first', 'second', 'manual'])
                ->default('not attempt')
                ->comment("not attempt: not attempt to fire MIT. first: first attempt to fire MIT. second: second attempt to fire MIT. manual: manual collection.");

            $table->enum('status', ['generated','sent'])->default('generated');
            $table->boolean('cleared')->default(false)->comment("If cleared, no need to fire again.");
            $table->datetime('cleared_at')->nullable();
            $table->foreignId('cleared_by')->nullable()->constrained('users')->restrictOnDelete()->comment('IF MANUAL COLLECTION. User who cleared the MIT.');
            $table->timestamps();

            $table->unique(['subscribable_id', 'invoice_number', 'status']);
        });
    }
};
