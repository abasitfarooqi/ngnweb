<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_change_requests', function (Blueprint $table): void {
            if (! Schema::hasColumn('document_change_requests', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('customer_profile_id')->index();
            }
        });

        DB::statement('
            UPDATE document_change_requests dcr
            INNER JOIN customer_profiles cp ON cp.id = dcr.customer_profile_id
            INNER JOIN customer_auths ca ON ca.id = cp.customer_auth_id
            SET dcr.customer_id = ca.customer_id
            WHERE dcr.customer_id IS NULL
        ');
    }

    public function down(): void
    {
        Schema::table('document_change_requests', function (Blueprint $table): void {
            if (Schema::hasColumn('document_change_requests', 'customer_id')) {
                $table->dropColumn('customer_id');
            }
        });
    }
};
