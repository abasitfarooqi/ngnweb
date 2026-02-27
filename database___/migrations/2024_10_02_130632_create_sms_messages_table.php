<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->id();
            $table->string('sid', 34)->nullable();
            $table->string('account_sid')->nullable();
            $table->string('api_version', 10);
            $table->text('body');
            $table->timestamp('date_created');
            $table->timestamp('date_sent')->nullable();
            $table->timestamp('date_updated')->nullable();
            $table->string('direction', 20);
            $table->string('error_code', 10)->nullable();
            $table->string('error_message', 255)->nullable();
            $table->string('from', 15);
            $table->string('to', 15);
            $table->string('messaging_service_sid', 34)->nullable();
            $table->integer('num_media')->default(0);
            $table->integer('num_segments')->default(1);
            $table->decimal('price', 8, 4)->nullable();
            $table->string('price_unit', 3)->nullable();
            $table->string('status', 20);
            $table->string('uri', 255);
            $table->json('subresource_uris')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_messages');
    }
};
