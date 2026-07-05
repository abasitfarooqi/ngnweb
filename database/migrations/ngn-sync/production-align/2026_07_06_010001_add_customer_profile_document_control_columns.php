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
            if (! Schema::hasColumn('customers', 'profile_initialised_at')) {
                $table->timestamp('profile_initialised_at')->nullable();
            }
            if (! Schema::hasColumn('customers', 'profile_editing_unlocked')) {
                $table->boolean('profile_editing_unlocked')->default(false);
            }
            if (! Schema::hasColumn('customers', 'document_reupload_unlocked')) {
                $table->boolean('document_reupload_unlocked')->default(false);
            }
        });
    }

    public function down(): void
    {
        // Manual rollback only.
    }
};
