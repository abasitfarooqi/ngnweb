<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('customers')) {
            return;
        }

        Schema::table('customers', function (Blueprint $table): void {
            if (! Schema::hasColumn('customers', 'preferred_branch_id')) {
                $table->unsignedBigInteger('preferred_branch_id')->nullable()->index();
            }
            if (! Schema::hasColumn('customers', 'verification_status')) {
                $table->string('verification_status', 40)->default('pending')->index();
            }
            if (! Schema::hasColumn('customers', 'verified_at')) {
                $table->timestamp('verified_at')->nullable();
            }
            if (! Schema::hasColumn('customers', 'verification_expires_at')) {
                $table->timestamp('verification_expires_at')->nullable();
            }
            if (! Schema::hasColumn('customers', 'locked_fields')) {
                $table->json('locked_fields')->nullable();
            }
            if (! Schema::hasColumn('customers', 'current_terms_version_id')) {
                $table->unsignedBigInteger('current_terms_version_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('customers')) {
            return;
        }

        Schema::table('customers', function (Blueprint $table): void {
            foreach ([
                'current_terms_version_id',
                'locked_fields',
                'verification_expires_at',
                'verified_at',
                'verification_status',
                'preferred_branch_id',
            ] as $column) {
                if (Schema::hasColumn('customers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
