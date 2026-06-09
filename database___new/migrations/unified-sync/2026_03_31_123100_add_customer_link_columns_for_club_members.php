<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('club_members', function (Blueprint $table): void {
            if (! Schema::hasColumn('club_members', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('user_id')->index();
            }
        });

        Schema::table('customers', function (Blueprint $table): void {
            if (! Schema::hasColumn('customers', 'is_club')) {
                $table->boolean('is_club')->default(false)->after('is_register')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            if (Schema::hasColumn('customers', 'is_club')) {
                $table->dropColumn('is_club');
            }
        });

        Schema::table('club_members', function (Blueprint $table): void {
            if (Schema::hasColumn('club_members', 'customer_id')) {
                $table->dropColumn('customer_id');
            }
        });
    }
};
