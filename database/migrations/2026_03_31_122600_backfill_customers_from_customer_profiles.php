<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $profiles = DB::table('customer_profiles as cp')
            ->join('customer_auths as ca', 'ca.id', '=', 'cp.customer_auth_id')
            ->select(
                'cp.*',
                'ca.customer_id as linked_customer_id',
            )
            ->whereNotNull('ca.customer_id')
            ->orderBy('cp.id')
            ->get();

        foreach ($profiles as $profile) {
            $customer = DB::table('customers')->where('id', $profile->linked_customer_id)->first();
            if (! $customer) {
                continue;
            }

            $updates = [];
            $map = [
                'first_name',
                'last_name',
                'phone',
                'whatsapp',
                'dob',
                'nationality',
                'license_number',
                'license_expiry_date',
                'license_issuance_authority',
                'license_issuance_date',
                'address',
                'postcode',
                'city',
                'country',
                'preferred_branch_id',
                'verification_status',
                'verified_at',
                'verification_expires_at',
                'reputation_note',
                'rating',
                'is_register',
            ];

            foreach ($map as $column) {
                if ($customer->{$column} === null && $profile->{$column} !== null) {
                    $updates[$column] = $profile->{$column};
                }
            }

            if ($customer->locked_fields === null && $profile->locked_fields !== null) {
                $updates['locked_fields'] = $profile->locked_fields;
            }

            if ($customer->emergency_contact === null && $profile->emergency_contact !== null) {
                $updates['emergency_contact'] = is_string($profile->emergency_contact)
                    ? $profile->emergency_contact
                    : json_encode($profile->emergency_contact);
            }

            if ($updates !== []) {
                DB::table('customers')
                    ->where('id', $profile->linked_customer_id)
                    ->update($updates);
            }
        }
    }

    public function down(): void
    {
        // Intentionally no-op: backfill is data migration.
    }
};
