<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communication_definitions', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('classification')->default('transactional')->index();
            $table->string('category')->default('general')->index();
            $table->string('priority')->default('normal')->index();
            $table->string('source_class')->nullable();
            $table->string('source_trigger')->nullable();
            $table->string('email_class')->nullable();
            $table->string('template_view')->nullable();
            $table->text('recipient_summary')->nullable();
            $table->json('supported_channels')->nullable();
            $table->json('variables')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('existing_email_default')->default(true);
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('communication_policies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('communication_definition_id')
                ->unique()
                ->constrained('communication_definitions')
                ->cascadeOnDelete();
            $table->boolean('email_enabled')->default(true);
            $table->boolean('internal_inbox_enabled')->default(false);
            $table->boolean('web_push_enabled')->default(false);
            $table->boolean('mobile_push_enabled')->default(false);
            $table->boolean('reply_allowed')->default(false);
            $table->boolean('enquiry_allowed')->default(false);
            $table->boolean('mandatory')->default(false);
            $table->string('priority')->default('normal');
            $table->timestamps();
        });

        Schema::create('communications', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('communication_definition_id')->nullable()->constrained('communication_definitions')->nullOnDelete();
            $table->string('communication_key')->index();
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->unsignedBigInteger('customer_auth_id')->nullable()->index();
            $table->string('recipient_email')->nullable()->index();
            $table->string('subject')->nullable();
            $table->string('title');
            $table->text('preview')->nullable();
            $table->longText('content_html')->nullable();
            $table->longText('content_text')->nullable();
            $table->json('structured_content')->nullable();
            $table->json('payload_snapshot')->nullable();
            $table->json('policy_snapshot')->nullable();
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('correlation_id')->nullable();
            $table->unsignedInteger('template_version')->nullable();
            $table->string('priority')->default('normal')->index();
            $table->string('category')->default('general')->index();
            $table->timestamps();

            $table->index(['customer_id', 'created_at']);
            $table->index(['customer_auth_id', 'created_at']);
            $table->index(['source_type', 'source_id']);
            $table->index(['communication_key', 'created_at']);
            $table->unique(['communication_key', 'correlation_id'], 'communications_key_correlation_unique');
        });

        Schema::create('communication_recipients', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('communication_id')->constrained('communications')->cascadeOnDelete();
            $table->unsignedBigInteger('customer_auth_id')->index();
            $table->timestamp('seen_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();

            $table->unique(['communication_id', 'customer_auth_id'], 'comm_recipients_comm_customer_unique');
            $table->index(['customer_auth_id', 'read_at']);
        });

        Schema::create('communication_deliveries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('communication_id')->constrained('communications')->cascadeOnDelete();
            $table->string('channel')->index();
            $table->string('status')->default('pending')->index();
            $table->string('provider')->nullable();
            $table->string('provider_message_id')->nullable()->index();
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->unsignedInteger('retry_count')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('communication_attachments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('communication_id')->constrained('communications')->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('disk')->default('private');
            $table->string('path');
            $table->string('filename');
            $table->string('display_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('checksum')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('communication_audits', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('actor_user_id')->nullable()->index();
            $table->foreignId('communication_definition_id')->nullable()->constrained('communication_definitions')->nullOnDelete();
            $table->string('event')->index();
            $table->string('field')->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communication_audits');
        Schema::dropIfExists('communication_attachments');
        Schema::dropIfExists('communication_deliveries');
        Schema::dropIfExists('communication_recipients');
        Schema::dropIfExists('communications');
        Schema::dropIfExists('communication_policies');
        Schema::dropIfExists('communication_definitions');
    }
};
