<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('club_members', 'customer_id') || ! Schema::hasTable('customers')) {
            return;
        }

        if ($this->constraintExists('club_members', 'club_members_customer_id_foreign')) {
            return;
        }

        Schema::table('club_members', function (Blueprint $table): void {
            $table->foreign('customer_id', 'club_members_customer_id_foreign')
                ->references('id')
                ->on('customers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! $this->constraintExists('club_members', 'club_members_customer_id_foreign')) {
            return;
        }

        Schema::table('club_members', function (Blueprint $table): void {
            $table->dropForeign('club_members_customer_id_foreign');
        });
    }

    protected function constraintExists(string $table, string $name): bool
    {
        $schema = Schema::getConnection()->getDatabaseName();
        $row = DB::selectOne(
            'SELECT COUNT(*) AS total FROM information_schema.TABLE_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = ?',
            [$schema, $table, $name, 'FOREIGN KEY']
        );

        return ((int) ($row->total ?? 0)) > 0;
    }
};
