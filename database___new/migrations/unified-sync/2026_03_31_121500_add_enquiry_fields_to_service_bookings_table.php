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
            if (! Schema::hasColumn('service_bookings', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('id')->index();
            }
            if (! Schema::hasColumn('service_bookings', 'customer_auth_id')) {
                $table->unsignedBigInteger('customer_auth_id')->nullable()->after('customer_id')->index();
            }
            if (! Schema::hasColumn('service_bookings', 'enquiry_type')) {
                $table->string('enquiry_type', 80)->nullable()->after('customer_auth_id')->index();
            }
            if (! Schema::hasColumn('service_bookings', 'subject')) {
                $table->string('subject')->nullable()->after('service_type');
            }
        });

        DB::statement("
            UPDATE service_bookings
            SET subject = COALESCE(subject, service_type)
            WHERE subject IS NULL OR subject = ''
        ");

        DB::statement("
            UPDATE service_bookings
            SET enquiry_type = CASE
                WHEN LOWER(CONCAT(COALESCE(service_type,''), ' ', COALESCE(description,''))) LIKE '%used bike%' THEN 'used_bike'
                WHEN LOWER(CONCAT(COALESCE(service_type,''), ' ', COALESCE(description,''))) LIKE '%new bike%' THEN 'new_bike'
                WHEN LOWER(CONCAT(COALESCE(service_type,''), ' ', COALESCE(description,''))) LIKE '%finance%' THEN 'finance'
                WHEN LOWER(CONCAT(COALESCE(service_type,''), ' ', COALESCE(description,''))) LIKE '%mot%' THEN 'mot'
                WHEN LOWER(CONCAT(COALESCE(service_type,''), ' ', COALESCE(description,''))) LIKE '%repair%' THEN 'service'
                WHEN LOWER(CONCAT(COALESCE(service_type,''), ' ', COALESCE(description,''))) LIKE '%service booking%' THEN 'service'
                WHEN LOWER(CONCAT(COALESCE(service_type,''), ' ', COALESCE(description,''))) LIKE '%delivery%' THEN 'recovery_delivery'
                WHEN LOWER(CONCAT(COALESCE(service_type,''), ' ', COALESCE(description,''))) LIKE '%recovery%' THEN 'recovery_delivery'
                WHEN LOWER(CONCAT(COALESCE(service_type,''), ' ', COALESCE(description,''))) LIKE '%rental%' THEN 'rental'
                WHEN LOWER(CONCAT(COALESCE(service_type,''), ' ', COALESCE(description,''))) LIKE '%rent%' THEN 'rental'
                ELSE COALESCE(enquiry_type, 'general')
            END
            WHERE enquiry_type IS NULL OR enquiry_type = ''
        ");
    }

    public function down(): void
    {
        Schema::table('service_bookings', function (Blueprint $table): void {
            if (Schema::hasColumn('service_bookings', 'subject')) {
                $table->dropColumn('subject');
            }
            if (Schema::hasColumn('service_bookings', 'enquiry_type')) {
                $table->dropColumn('enquiry_type');
            }
            if (Schema::hasColumn('service_bookings', 'customer_auth_id')) {
                $table->dropColumn('customer_auth_id');
            }
            if (Schema::hasColumn('service_bookings', 'customer_id')) {
                $table->dropColumn('customer_id');
            }
        });
    }
};
