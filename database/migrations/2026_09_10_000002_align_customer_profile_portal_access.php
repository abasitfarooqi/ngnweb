<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('customer_profiles')) {
            return;
        }

        Schema::table('customer_profiles', function (Blueprint $table): void {
            if (! Schema::hasColumn('customer_profiles', 'is_active')) {
                $table->boolean('is_active')->default(true)->index();
            }
            if (! Schema::hasColumn('customer_profiles', 'portal_upload_access')) {
                $table->boolean('portal_upload_access')->default(false)->index();
            }
        });
    }

    public function down(): void
    {
        // Production rollback is manual because schemas are synchronised externally.
    }
};
