<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('customers', 'license_number')) {
            return;
        }
        Schema::table('customers', function (Blueprint $table) {
            $table->string('license_number')->nullable();
            $table->date('license_expiry_date')->nullable();
            $table->string('license_issuance_authority')->nullable();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('customers', 'license_number')) {
            return;
        }
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('license_number');
            $table->dropColumn('license_expiry_date');
            $table->dropColumn('license_issuance_authority');
        });
    }
};
