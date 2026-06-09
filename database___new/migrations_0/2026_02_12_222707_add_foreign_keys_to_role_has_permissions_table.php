<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $db = DB::getDatabaseName();
        $existsP = DB::selectOne(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?',
            [$db, 'role_has_permissions', 'role_has_permissions_permission_id_foreign']
        );
        $existsR = DB::selectOne(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?',
            [$db, 'role_has_permissions', 'role_has_permissions_role_id_foreign']
        );
        if ($existsP && $existsR) {
            return;
        }
        Schema::table('role_has_permissions', function (Blueprint $table) {
            if (! $existsP) {
                $table->foreign(['permission_id'])->references(['id'])->on('permissions')->onUpdate('restrict')->onDelete('cascade');
            }
            if (! $existsR) {
                $table->foreign(['role_id'])->references(['id'])->on('roles')->onUpdate('restrict')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->dropForeign('role_has_permissions_permission_id_foreign');
            $table->dropForeign('role_has_permissions_role_id_foreign');
        });
    }
};
