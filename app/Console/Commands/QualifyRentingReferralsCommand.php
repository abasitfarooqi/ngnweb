<?php

namespace App\Console\Commands;

use App\Models\BookingInvoice;
use App\Models\Customer;
use App\Models\RentingReferral;
use App\Services\Renting\RentingReferralService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class QualifyRentingReferralsCommand extends Command
{
    protected $signature = 'renting-referrals:qualify';

    protected $description = 'Match and qualify rental referrals from paid weekly invoices';

    public function handle(RentingReferralService $service): int
    {
        if (! Schema::hasTable('renting_referrals')) {
            $this->warn('renting_referrals table is not installed.');

            return self::SUCCESS;
        }

        $open = RentingReferral::query()
            ->whereIn('status', [
                RentingReferral::STATUS_SUBMITTED,
                RentingReferral::STATUS_MATCHED,
                RentingReferral::STATUS_QUALIFYING,
            ])
            ->orderBy('id')
            ->get();

        $this->info('Checking '.$open->count().' open referrals.');

        foreach ($open as $referral) {
            try {
                if ($referral->referred_customer_id) {
                    $customer = Customer::query()->find($referral->referred_customer_id);
                    if ($customer) {
                        $service->syncCustomer($customer);
                    }
                } else {
                    $service->refreshOpenReferral($referral);
                }
            } catch (\Throwable $e) {
                $this->error('Referral #'.$referral->id.': '.$e->getMessage());
            }
        }

        $recentPaid = BookingInvoice::query()
            ->where('is_paid', true)
            ->where('amount', '>', 0)
            ->where('paid_date', '>=', now()->subDays(14))
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        foreach ($recentPaid as $invoice) {
            $service->syncPaidInvoice($invoice);
        }

        $this->info('Done.');

        return self::SUCCESS;
    }
}
