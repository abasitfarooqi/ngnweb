<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            if (! Schema::hasColumn('customers', 'preferred_branch_id')) {
                $table->unsignedBigInteger('preferred_branch_id')->nullable()->after('country')->index();
            }
            if (! Schema::hasColumn('customers', 'verification_status')) {
                $table->string('verification_status', 40)->nullable()->after('preferred_branch_id')->index();
            }
            if (! Schema::hasColumn('customers', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verification_status');
            }
            if (! Schema::hasColumn('customers', 'verification_expires_at')) {
                $table->timestamp('verification_expires_at')->nullable()->after('verified_at');
            }
            if (! Schema::hasColumn('customers', 'locked_fields')) {
                $table->json('locked_fields')->nullable()->after('verification_expires_at');
            }
            if (! Schema::hasColumn('customers', 'current_terms_version_id')) {
                $table->unsignedBigInteger('current_terms_version_id')->nullable()->after('locked_fields');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            if (Schema::hasColumn('customers', 'current_terms_version_id')) {
                $table->dropColumn('current_terms_version_id');
            }
            if (Schema::hasColumn('customers', 'locked_fields')) {
                $table->dropColumn('locked_fields');
            }
            if (Schema::hasColumn('customers', 'verification_expires_at')) {
                $table->dropColumn('verification_expires_at');
            }
            if (Schema::hasColumn('customers', 'verified_at')) {
                $table->dropColumn('verified_at');
            }
            if (Schema::hasColumn('customers', 'verification_status')) {
                $table->dropColumn('verification_status');
            }
            if (Schema::hasColumn('customers', 'preferred_branch_id')) {
                $table->dropColumn('preferred_branch_id');
            }
        });
    }
};
