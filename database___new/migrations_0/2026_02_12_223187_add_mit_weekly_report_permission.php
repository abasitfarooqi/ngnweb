<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create permission for receiving weekly MIT reports (only if it doesn't exist)
        $permission = Permission::firstOrCreate(
            [
                'name' => 'can-receive-mit-weekly-reports',
                'guard_name' => 'web',
            ],
            [
                'group_name' => 'Judopay MIT',
                'display_name' => 'Receive Weekly MIT Reports',
                'description' => 'Receive weekly MIT collection and decline reports via email (Monday opening + Sunday closing)',
                'can_be_removed' => true,
            ]
        );

        // Add this user to that permission (user ID 129)
        $user = User::find(129);
        if ($user && !$user->hasPermissionTo('can-receive-mit-weekly-reports')) {
            $user->givePermissionTo('can-receive-mit-weekly-reports');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove permission from user 129
        $user = User::find(129);
        if ($user) {
            $user->revokePermissionTo('can-receive-mit-weekly-reports');
        }

        // Delete the permission
        Permission::where('name', 'can-receive-mit-weekly-reports')
            ->where('guard_name', 'web')
            ->delete();
    }
};
