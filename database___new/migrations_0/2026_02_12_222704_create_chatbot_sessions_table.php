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
        Schema::create('chatbot_sessions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('session_id', 100)->index();
            $table->text('user_message');
            $table->text('bot_response');
            $table->text('admin_reply')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable()->index();
            $table->timestamp('admin_replied_at')->nullable();
            $table->boolean('bot_disabled')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_typing')->default(false);
            $table->string('message_status')->default('sent');
            $table->integer('message_order')->default(1);
            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('user_ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable();

            $table->index(['session_id', 'bot_disabled']);
            $table->index(['session_id', 'message_order']);
            $table->index(['session_id', 'message_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_sessions');
    }
};
