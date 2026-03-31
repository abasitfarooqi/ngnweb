<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $clubMembers = DB::table('club_members')
            ->select('id', 'email', 'phone')
            ->whereNull('customer_id')
            ->orderBy('id')
            ->get();

        foreach ($clubMembers as $member) {
            $email = strtolower(trim((string) $member->email));
            $phone = preg_replace('/\s+/', '', (string) $member->phone);
            $phone = preg_replace('/^\+44/', '0', $phone);

            $customer = DB::table('customers')
                ->whereRaw('LOWER(TRIM(email)) = ?', [$email])
                ->whereRaw("REPLACE(REPLACE(phone, ' ', ''), '+44', '0') = ?", [$phone])
                ->first();

            if (! $customer) {
                continue;
            }

            DB::table('club_members')
                ->where('id', $member->id)
                ->update(['customer_id' => $customer->id]);

            DB::table('customers')
                ->where('id', $customer->id)
                ->update(['is_club' => true]);
        }
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
