<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_bookings', function (Blueprint $table): void {
            if (! Schema::hasColumn('service_bookings', 'submission_context')) {
                $table->string('submission_context', 40)->nullable()->after('customer_auth_id')->index();
            }
        });

        DB::statement("
            UPDATE service_bookings
            SET submission_context = CASE
                WHEN customer_auth_id IS NOT NULL OR customer_id IS NOT NULL THEN 'authenticated_customer'
                ELSE 'guest'
            END
            WHERE submission_context IS NULL OR submission_context = ''
        ");
    }

    public function down(): void
    {
        Schema::table('service_bookings', function (Blueprint $table): void {
            if (Schema::hasColumn('service_bookings', 'submission_context')) {
                $table->dropColumn('submission_context');
            }
        });
    }
};
