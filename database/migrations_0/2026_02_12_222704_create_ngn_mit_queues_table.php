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
        Schema::create('ngn_mit_queues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('subscribable_id');
            $table->string('invoice_number');
            $table->date('invoice_date')->comment('Invoice due date. When is the invoice amount is due.');
            $table->dateTime('mit_fire_date')->comment('MIT fire date. When is the MIT fire date.');
            $table->enum('mit_attempt', ['not attempt', 'first', 'second', 'manual'])->default('not attempt')->comment('not attempt: not attempt to fire MIT. first: first attempt to fire MIT. second: second attempt to fire MIT. manual: manual collection.');
            $table->enum('status', ['generated', 'sent'])->default('generated');
            $table->boolean('cleared')->default(false)->comment('If cleared, no need to fire again.');
            $table->dateTime('cleared_at')->nullable();
            $table->unsignedBigInteger('cleared_by')->nullable()->index('ngn_mit_queues_cleared_by_foreign');
            $table->timestamps();

            $table->unique(['subscribable_id', 'invoice_number', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngn_mit_queues');
    }
};
