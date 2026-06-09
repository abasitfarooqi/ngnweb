<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('judopay_onboardings', function (Blueprint $table) {
            $table->id();
            $table->morphs('onboardable');
            $table->boolean('is_onboarded')->default(false)->comment('If the customer subscribes to Judopay and CIT reponse is success with card token and receipt-id');
            $table->timestamps();

            $table->unique(['onboardable_id', 'onboardable_type']);
        });
    }
};
