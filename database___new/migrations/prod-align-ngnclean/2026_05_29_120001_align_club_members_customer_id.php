<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Production alignment: club_members.customer_id + customers.is_club */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('club_members')) {
            return;
        }

        Schema::table('club_members', function (Blueprint $table): void {
            if (! Schema::hasColumn('club_members', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('user_id')->index();
            }
        });

        if (! Schema::hasTable('customers')) {
            return;
        }

        Schema::table('customers', function (Blueprint $table): void {
            if (! Schema::hasColumn('customers', 'is_club')) {
                $table->boolean('is_club')->default(false)->after('is_register')->index();
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('customers', 'is_club')) {
            Schema::table('customers', function (Blueprint $table): void {
                $table->dropColumn('is_club');
            });
        }

        if (Schema::hasColumn('club_members', 'customer_id')) {
            Schema::table('club_members', function (Blueprint $table): void {
                $table->dropColumn('customer_id');
            });
        }
    }
};
