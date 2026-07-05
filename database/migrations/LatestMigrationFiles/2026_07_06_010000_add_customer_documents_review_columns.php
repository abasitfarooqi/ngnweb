<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Aligns customer_documents with App\Models\CustomerDocument (status workflow).
 * Safe to run on production — skips columns that already exist.
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
            //
        }

        try {
            Schema::table('customer_documents', function (Blueprint $table): void {
                $table->index('status', 'customer_documents_status_index');
            });
        } catch (\Throwable) {
            //
        }

        if (Schema::hasColumn('customer_documents', 'status') && Schema::hasColumn('customer_documents', 'is_verified')) {
            \Illuminate\Support\Facades\DB::table('customer_documents')
                ->whereNull('status')
                ->orWhere('status', '')
                ->update(['status' => 'pending_review']);

            \Illuminate\Support\Facades\DB::table('customer_documents')
                ->where('is_verified', true)
                ->whereIn('status', ['pending_review', 'uploaded'])
                ->update(['status' => 'approved']);
        }
    }

    public function down(): void
    {
        // Manual rollback only.
    }
};
