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
        Schema::table('customer_documents', function (Blueprint $table) {
            // Add reviewer and review tracking
            if (!Schema::hasColumn('customer_documents', 'reviewer_id')) {
                $table->foreignId('reviewer_id')->nullable()->after('is_verified')->constrained('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('customer_documents', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewer_id');
            }
            
            if (!Schema::hasColumn('customer_documents', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('reviewed_at');
            }
            
            if (!Schema::hasColumn('customer_documents', 'status')) {
                $table->enum('status', ['uploaded', 'pending_review', 'approved', 'rejected', 'archived'])
                    ->default('pending_review')
                    ->after('rejection_reason');
            }
            
            // Add index for common queries
            if (!Schema::hasColumn('customer_documents', 'status')) {
                $table->index('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_documents', function (Blueprint $table) {
            $table->dropForeign(['reviewer_id']);
            $table->dropColumn(['reviewer_id', 'reviewed_at', 'rejection_reason', 'status']);
        });
    }
};
