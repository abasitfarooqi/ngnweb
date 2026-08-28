<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('communication_policies') && ! Schema::hasColumn('communication_policies', 'staff_copy_enabled')) {
            Schema::table('communication_policies', function (Blueprint $table): void {
                $table->boolean('staff_copy_enabled')->default(false)->after('internal_inbox_enabled');
            });
        }

        if (Schema::hasTable('communications') && ! Schema::hasColumn('communications', 'staff_hidden_at')) {
            Schema::table('communications', function (Blueprint $table): void {
                $table->timestamp('staff_hidden_at')->nullable()->index()->after('category');
                $table->unsignedBigInteger('staff_hidden_by')->nullable()->after('staff_hidden_at');
            });
        }

        Permission::findOrCreate('view-notifications', 'web');
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        if (Schema::hasTable('communication_policies') && Schema::hasColumn('communication_policies', 'staff_copy_enabled')) {
            Schema::table('communication_policies', function (Blueprint $table): void {
                $table->dropColumn('staff_copy_enabled');
            });
        }

        if (Schema::hasTable('communications') && Schema::hasColumn('communications', 'staff_hidden_at')) {
            Schema::table('communications', function (Blueprint $table): void {
                $table->dropColumn(['staff_hidden_at', 'staff_hidden_by']);
            });
        }

        Permission::query()
            ->where('name', 'view-notifications')
            ->where('guard_name', 'web')
            ->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
