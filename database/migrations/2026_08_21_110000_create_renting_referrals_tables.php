<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('renting_referrals')) {
            Schema::create('renting_referrals', function (Blueprint $table): void {
                $table->id();
                $table->string('referral_code', 16)->unique();
                $table->foreignId('referrer_customer_id')->constrained('customers')->restrictOnDelete();
                $table->string('submitted_name');
                $table->string('submitted_phone', 32);
                $table->string('submitted_email')->nullable();
                $table->foreignId('referred_customer_id')->nullable()->constrained('customers')->nullOnDelete();
                $table->string('status', 24);
                $table->string('source', 16);
                $table->foreignId('referrer_qualifying_booking_id')->nullable()->constrained('renting_bookings')->nullOnDelete();
                $table->foreignId('referred_qualifying_booking_id')->nullable()->constrained('renting_bookings')->nullOnDelete();
                $table->foreignId('referred_qualifying_invoice_id')->nullable()->constrained('booking_invoices')->nullOnDelete();
                $table->timestamp('matched_at')->nullable();
                $table->timestamp('qualified_at')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->text('review_reason')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->json('warnings')->nullable();
                $table->timestamps();

                $table->index(['referrer_customer_id', 'status']);
                $table->index(['referred_customer_id', 'status']);
                $table->index(['status', 'created_at']);
                $table->index('submitted_phone');
            });
        }

        if (! Schema::hasTable('renting_referral_point_ledger')) {
            Schema::create('renting_referral_point_ledger', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
                $table->foreignId('referral_id')->nullable()->constrained('renting_referrals')->nullOnDelete();
                $table->string('direction', 8);
                $table->string('status', 16);
                $table->unsignedInteger('points');
                $table->timestamp('available_from')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('approved_at')->nullable();
                $table->foreignId('released_early_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('released_early_at')->nullable();
                $table->text('release_reason')->nullable();
                $table->timestamp('original_available_from')->nullable();
                $table->foreignId('redeemed_booking_id')->nullable()->constrained('renting_bookings')->nullOnDelete();
                $table->foreignId('redeemed_invoice_id')->nullable()->constrained('booking_invoices')->nullOnDelete();
                $table->foreignId('redeemed_transaction_id')->nullable()->constrained('renting_transactions')->nullOnDelete();
                $table->timestamps();

                $table->index(['customer_id', 'status']);
                $table->index(['referral_id', 'direction', 'status']);
                $table->unique('redeemed_invoice_id', 'renting_referral_ledger_invoice_uidx');
                $table->unique(['referral_id', 'direction'], 'renting_referral_ledger_referral_dir_uidx');
            });
        }

        if (! Schema::hasTable('renting_referral_logs')) {
            Schema::create('renting_referral_logs', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('referral_id')->index();
                $table->string('action', 32);
                $table->json('old_data')->nullable();
                $table->json('new_data')->nullable();
                $table->unsignedBigInteger('changed_by')->nullable()->index();
                $table->timestamp('created_at')->nullable();
            });
        }

        if (Schema::hasTable('transaction_types')) {
            $exists = DB::table('transaction_types')
                ->where('type', 'Rental referral reward')
                ->exists();

            if (! $exists) {
                DB::table('transaction_types')->insert([
                    'type' => 'Rental referral reward',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if (Schema::hasTable('system_settings')) {
            $key = 'renting_referrals';
            $exists = DB::table('system_settings')->where('key', $key)->exists();

            if (! $exists) {
                DB::table('system_settings')->insert([
                    'key' => $key,
                    'display_name' => 'Renting referrals',
                    'value' => json_encode([
                        'points_per_qualified_referral' => 100,
                        'wait_days' => 14,
                        'early_release_allowed' => true,
                    ]),
                    'locked' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Permission::findOrCreate('rental-referrals-review', 'web');

        try {
            foreach (['Admin', 'Manager'] as $roleName) {
                $role = Role::query()->where('name', $roleName)->where('guard_name', 'web')->first();
                if ($role && ! $role->hasPermissionTo('rental-referrals-review')) {
                    $role->givePermissionTo('rental-referrals-review');
                }
            }
        } catch (\Throwable) {
            // Roles may not exist in every environment.
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Schema::dropIfExists('renting_referral_logs');
        Schema::dropIfExists('renting_referral_point_ledger');
        Schema::dropIfExists('renting_referrals');

        Permission::query()
            ->where('name', 'rental-referrals-review')
            ->where('guard_name', 'web')
            ->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
