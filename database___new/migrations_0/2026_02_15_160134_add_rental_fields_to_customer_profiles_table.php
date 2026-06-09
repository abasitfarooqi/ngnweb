<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customer_profiles', function (Blueprint $table) {
            // License information (required for rentals)
            $table->string('license_number')->nullable()->after('nationality');
            $table->date('license_expiry_date')->nullable()->after('license_number');
            $table->string('license_issuance_authority')->nullable()->after('license_expiry_date');
            $table->date('license_issuance_date')->nullable()->after('license_issuance_authority');
            
            // Additional rental fields
            $table->text('reputation_note')->nullable()->after('locked_fields');
            $table->integer('rating')->nullable()->default(0)->after('reputation_note');
            $table->boolean('is_register')->default(false)->after('rating');
            
            // Add indexes for common queries
            $table->index('license_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'license_number',
                'license_expiry_date',
                'license_issuance_authority',
                'license_issuance_date',
                'reputation_note',
                'rating',
                'is_register',
            ]);
        });
    }
};
