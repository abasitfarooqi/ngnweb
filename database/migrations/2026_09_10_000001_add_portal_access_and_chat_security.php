<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table): void {
                if (! Schema::hasColumn('customers', 'is_active')) {
                    $table->boolean('is_active')->default(true)->index();
                }
                if (! Schema::hasColumn('customers', 'portal_upload_access')) {
                    $table->boolean('portal_upload_access')->default(false)->index();
                }
            });
        }

        if (Schema::hasTable('customer_auths')) {
            Schema::table('customer_auths', function (Blueprint $table): void {
                if (! Schema::hasColumn('customer_auths', 'is_active')) {
                    $table->boolean('is_active')->default(true)->index();
                }
            });
        }

        if (Schema::hasTable('support_messages')) {
            Schema::table('support_messages', function (Blueprint $table): void {
                if (! Schema::hasColumn('support_messages', 'reply_to_message_id')) {
                    $table->foreignId('reply_to_message_id')->nullable()->after('body')->constrained('support_messages')->nullOnDelete();
                }
                if (! Schema::hasColumn('support_messages', 'deleted_at')) {
                    $table->timestamp('deleted_at')->nullable()->index();
                    $table->unsignedBigInteger('deleted_by_user_id')->nullable();
                    $table->string('deleted_by_role', 80)->nullable();
                }
            });
        }

        if (Schema::hasTable('support_attachments')) {
            Schema::table('support_attachments', function (Blueprint $table): void {
                if (! Schema::hasColumn('support_attachments', 'deleted_at')) {
                    $table->timestamp('deleted_at')->nullable()->index();
                    $table->unsignedBigInteger('deleted_by_user_id')->nullable();
                    $table->string('deleted_by_role', 80)->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        // Production rollback is manual because schemas are also synchronised externally.
    }
};
