<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Aligns `customer_documents` with application expectations (see App\Models\CustomerDocument fillable):
 * rejection_reason, reviewer_id, reviewed_at, status (+ FK to users, index on status).
 *
 * Skips any column that already exists so it is safe across environments.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('customer_documents')) {
            return;
        }

        Schema::table('customer_documents', function (Blueprint $table): void {
            if (! Schema::hasColumn('customer_documents', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable();
            }
            if (! Schema::hasColumn('customer_documents', 'reviewer_id')) {
                $table->unsignedBigInteger('reviewer_id')->nullable();
            }
            if (! Schema::hasColumn('customer_documents', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable();
            }
            if (! Schema::hasColumn('customer_documents', 'status')) {
                $table->enum('status', ['uploaded', 'pending_review', 'approved', 'rejected', 'archived'])
                    ->default('pending_review');
            }
        });

        try {
            Schema::table('customer_documents', function (Blueprint $table): void {
                $table->foreign('reviewer_id')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            });
        } catch (\Throwable) {
            // Constraint may already exist.
        }

        try {
            Schema::table('customer_documents', function (Blueprint $table): void {
                $table->index('status', 'customer_documents_status_index');
            });
        } catch (\Throwable) {
            // Index may already exist.
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('customer_documents')) {
            return;
        }

        try {
            Schema::table('customer_documents', function (Blueprint $table): void {
                $table->dropForeign(['reviewer_id']);
            });
        } catch (\Throwable) {
            //
        }

        try {
            Schema::table('customer_documents', function (Blueprint $table): void {
                $table->dropIndex('customer_documents_status_index');
            });
        } catch (\Throwable) {
            //
        }

        $toDrop = array_values(array_filter(
            ['status', 'reviewed_at', 'reviewer_id', 'rejection_reason'],
            fn (string $col): bool => Schema::hasColumn('customer_documents', $col)
        ));

        if ($toDrop !== []) {
            Schema::table('customer_documents', function (Blueprint $table) use ($toDrop): void {
                $table->dropColumn($toDrop);
            });
        }
    }
};
