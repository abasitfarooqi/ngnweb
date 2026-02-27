<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {

            // Core fields
            if (!Schema::hasColumn('customers', 'first_name')) {
                $table->string('first_name')->nullable()->after('id');
            }
            if (!Schema::hasColumn('customers', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('customers', 'dob')) {
                $table->date('dob')->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('customers', 'email')) {
                $table->string('email')->nullable()->after('dob');
            }
            if (!Schema::hasColumn('customers', 'is_register')) {
                $table->boolean('is_register')->default(false)->after('email');
            }
            if (!Schema::hasColumn('customers', 'phone')) {
                $table->string('phone')->nullable()->after('is_register');
            }
            if (!Schema::hasColumn('customers', 'address')) {
                $table->string('address')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('customers', 'postcode')) {
                $table->string('postcode')->nullable()->after('address');
            }
            if (!Schema::hasColumn('customers', 'city')) {
                $table->string('city')->default('London')->after('postcode');
            }
            if (!Schema::hasColumn('customers', 'country')) {
                $table->string('country')->default('UK')->after('city');
            }

            // If timestamps are missing (common when schema differs)
            if (!Schema::hasColumn('customers', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('customers', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }

            // Extra fields
            if (!Schema::hasColumn('customers', 'nationality')) {
                $table->string('nationality')->nullable()->after('updated_at');
            }
            if (!Schema::hasColumn('customers', 'reputation_note')) {
                $table->text('reputation_note')->nullable()->after('nationality');
            }
            if (!Schema::hasColumn('customers', 'emergency_contact')) {
                $table->string('emergency_contact')->nullable()->comment('Name of the emergency contact person')->after('reputation_note');
            }
            if (!Schema::hasColumn('customers', 'whatsapp')) {
                $table->string('whatsapp')->nullable()->comment('Whatsapp number')->after('emergency_contact');
            }

            // NOTE: These column names contain spaces, exactly as your seed data expects.
            if (!Schema::hasColumn('customers', 'Customer Full Name')) {
                $table->string('Customer Full Name', 50)->nullable()->after('whatsapp');
            }
            if (!Schema::hasColumn('customers', 'last name')) {
                $table->string('last name', 50)->nullable()->after('Customer Full Name');
            }

            if (!Schema::hasColumn('customers', 'PHONE1')) {
                $table->integer('PHONE1')->nullable()->after('last name');
            }
            if (!Schema::hasColumn('customers', 'creatd')) {
                $table->string('creatd', 50)->nullable()->after('PHONE1');
            }
            if (!Schema::hasColumn('customers', 'updated')) {
                $table->string('updated', 50)->nullable()->after('creatd');
            }

            // Your list shows "whatsapp_number" (not "WHATSAPP NO.")
            if (!Schema::hasColumn('customers', 'whatsapp_number')) {
                $table->string('whatsapp_number', 50)->nullable()->after('updated');
            }

            if (!Schema::hasColumn('customers', 'rating')) {
                $table->integer('rating')->nullable()->after('whatsapp_number');
            }
            if (!Schema::hasColumn('customers', 'license_number')) {
                $table->string('license_number')->nullable()->after('rating');
            }
            if (!Schema::hasColumn('customers', 'license_expiry_date')) {
                $table->date('license_expiry_date')->nullable()->after('license_number');
            }
            if (!Schema::hasColumn('customers', 'license_issuance_authority')) {
                $table->string('license_issuance_authority')->nullable()->after('license_expiry_date');
            }
            if (!Schema::hasColumn('customers', 'license_issuance_date')) {
                $table->date('license_issuance_date')->nullable()->after('license_issuance_authority');
            }
        });

        // Email unique index: only add if there isn't already one.
        // Safer to avoid guessing existing index names.
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {

            // Drop in reverse-ish order (only if exists)
            $cols = [
                'license_issuance_date',
                'license_issuance_authority',
                'license_expiry_date',
                'license_number',
                'rating',
                'whatsapp_number',
                'updated',
                'creatd',
                'PHONE1',
                'last name',
                'Customer Full Name',
                'whatsapp',
                'emergency_contact',
                'reputation_note',
                'nationality',
                'country',
                'city',
                'postcode',
                'address',
                'phone',
                'is_register',
                'email',
                'dob',
                'last_name',
                'first_name',
            ];

            foreach ($cols as $col) {
                if (Schema::hasColumn('customers', $col)) {
                    $table->dropColumn($col);
                }
            }

            // timestamps: only drop if you truly want to revert
            if (Schema::hasColumn('customers', 'created_at')) $table->dropColumn('created_at');
            if (Schema::hasColumn('customers', 'updated_at')) $table->dropColumn('updated_at');
        });
    }
};
