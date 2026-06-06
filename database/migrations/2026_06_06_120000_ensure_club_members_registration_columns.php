<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('club_members')) {
            return;
        }

        Schema::table('club_members', function (Blueprint $table): void {
            if (! Schema::hasColumn('club_members', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('club_members', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('user_id')->index();
            }
            if (! Schema::hasColumn('club_members', 'make')) {
                $table->string('make', 50)->nullable()->after('customer_id');
            }
            if (! Schema::hasColumn('club_members', 'model')) {
                $table->string('model', 50)->nullable()->after('make');
            }
            if (! Schema::hasColumn('club_members', 'year')) {
                $table->string('year', 4)->nullable()->after('model');
            }
            if (! Schema::hasColumn('club_members', 'vrm')) {
                $table->string('vrm', 12)->nullable()->after('year');
            }
            if (! Schema::hasColumn('club_members', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('vrm');
            }
            if (! Schema::hasColumn('club_members', 'tc_agreed')) {
                $table->boolean('tc_agreed')->default(true)->after('is_active');
            }
            if (! Schema::hasColumn('club_members', 'passkey')) {
                $table->string('passkey', 10)->nullable()->after('tc_agreed');
            }
            if (! Schema::hasColumn('club_members', 'email_sent')) {
                $table->boolean('email_sent')->default(false)->after('passkey');
            }
            if (! Schema::hasColumn('club_members', 'ngn_partner_id')) {
                $table->unsignedBigInteger('ngn_partner_id')->nullable()->after('email_sent')->index();
            }
            if (! Schema::hasColumn('club_members', 'is_partner')) {
                $table->boolean('is_partner')->default(false)->after('ngn_partner_id');
            }
        });

        if (Schema::hasTable('customers') && ! Schema::hasColumn('customers', 'is_club')) {
            Schema::table('customers', function (Blueprint $table): void {
                $table->boolean('is_club')->default(false)->after('is_register')->index();
            });
        }
    }

    public function down(): void
    {
        // Intentionally no-op: production may have relied on these columns before this migration ran.
    }
};
