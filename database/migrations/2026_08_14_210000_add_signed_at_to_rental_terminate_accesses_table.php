<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rental_terminate_accesses', function (Blueprint $table) {
            if (! Schema::hasColumn('rental_terminate_accesses', 'signed_at')) {
                $table->timestamp('signed_at')->nullable()->after('expire_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rental_terminate_accesses', function (Blueprint $table) {
            if (Schema::hasColumn('rental_terminate_accesses', 'signed_at')) {
                $table->dropColumn('signed_at');
            }
        });
    }
};
