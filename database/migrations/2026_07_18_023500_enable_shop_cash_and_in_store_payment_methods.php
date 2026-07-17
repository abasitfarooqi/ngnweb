<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ec_payment_methods')) {
            return;
        }

        $now = now();

        DB::table('ec_payment_methods')->where('slug', 'paypal')->update([
            'is_enabled' => 1,
            'updated_at' => $now,
        ]);

        DB::table('ec_payment_methods')->where('slug', 'pay-on-store')->update([
            'title' => 'In Store Payment',
            'is_enabled' => 1,
            'instructions' => 'Pay when you collect your order at the branch.',
            'updated_at' => $now,
        ]);

        if (! DB::table('ec_payment_methods')->where('slug', 'cash')->exists()) {
            DB::table('ec_payment_methods')->insert([
                'title' => 'Cash',
                'slug' => 'cash',
                'logo' => '-',
                'link_url' => '-',
                'instructions' => 'Pay with cash when you collect your order at the branch.',
                'is_enabled' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            DB::table('ec_payment_methods')->where('slug', 'cash')->update([
                'is_enabled' => 1,
                'instructions' => 'Pay with cash when you collect your order at the branch.',
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('ec_payment_methods')) {
            return;
        }

        DB::table('ec_payment_methods')->where('slug', 'pay-on-store')->update(['is_enabled' => 0]);
        DB::table('ec_payment_methods')->where('slug', 'cash')->update(['is_enabled' => 0]);
    }
};
