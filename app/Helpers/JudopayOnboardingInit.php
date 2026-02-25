<?php

namespace App\Helpers;

use App\Models\Customer;
use App\Models\FinanceApplication;
use App\Models\JudopayOnboarding;
use App\Models\JudopaySubscription;
use App\Models\RentingBooking;

class JudopayOnboardingInit
{

    public static function init()
    {
        self::initCustomerOnboarding();
        self::initRentalSubscriptions();
        self::initFinanceSubscriptions();
    }

    private static function initCustomerOnboarding()
    {
        // Get customers who have active rentals
        $activeRentalIds = RentingBooking::getActiveRentals()->pluck('customer_id');
        $customersWithActiveRentals = Customer::whereIn('id', $activeRentalIds)->pluck('id');

        // Get customers who have active finance applications
        $activeFinanceIds = FinanceApplication::getActiveFinanceApplications()->pluck('customer_id');
        $customersWithActiveFinance = Customer::whereIn('id', $activeFinanceIds)->pluck('id');

        // Combine both customer lists
        $activeCustomers = $customersWithActiveRentals->merge($customersWithActiveFinance)->unique();

        // Get customers who already have onboarding records
        $existingOnboardingCustomers = JudopayOnboarding::where('onboardable_type', Customer::class)
            ->whereIn('onboardable_id', $activeCustomers)
            ->pluck('onboardable_id')
            ->toArray();

        // Find customers who need onboarding records
        $customersNeedingOnboarding = $activeCustomers->diff($existingOnboardingCustomers);

        if ($customersNeedingOnboarding->isNotEmpty()) {
            JudopayOnboarding::insert($customersNeedingOnboarding->map(function ($customerId) {
                return [
                    'onboardable_id' => $customerId,
                    'onboardable_type' => Customer::class,
                    'is_onboarded' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray());
        }
    }

    private static function initRentalSubscriptions()
    {
        // Get all active rentals with their items and customer info
        $activeRentals = RentingBooking::getActiveRentals()
            ->with(['customer:id,first_name,last_name', 'rentingBookingItems'])
            ->get();

        foreach ($activeRentals as $rental) {
            // Get the customer's onboarding record
            $onboarding = JudopayOnboarding::where('onboardable_type', Customer::class)
                ->where('onboardable_id', $rental->customer_id)
                ->first();

            if (! $onboarding) {
                continue; // Skip if no onboarding record
            }

            // Check if subscription already exists for this rental
            $existingSubscription = JudopaySubscription::where('subscribable_type', RentingBooking::class)
                ->where('subscribable_id', $rental->id)
                ->first();

            if ($existingSubscription) {
                continue; // Skip if subscription already exists
            }

            // Create subscription for each rental item
            foreach ($rental->rentingBookingItems as $item) {
                // Calculate billing day (1-7) from start_date
                $billingDay = $item->start_date ? $item->start_date->dayOfWeek : 1;

                // Create consumer reference via helper
                $consumerReference = JudopayReference::buildConsumerReference(
                    RentingBooking::class,
                    $rental,
                    'NGNR-'.$rental->id.'-'.$rental->customer_id
                );

                JudopaySubscription::create([
                    'judopay_onboarding_id' => $onboarding->id,
                    'date' => now()->toDateString(),
                    'subscribable_id' => $rental->id,
                    'subscribable_type' => RentingBooking::class,
                    'billing_frequency' => 'weekly',
                    'billing_day' => $billingDay,
                    'amount' => $item->weekly_rent,
                    'opening_balance' => 0, // Rentals don't have opening balance
                    'start_date' => $item->start_date,
                    'end_date' => null, // Rentals are ongoing
                    'status' => 'pending', // Default status
                    'consumer_reference' => $consumerReference,
                ]);
            }
        }
    }

    private static function initFinanceSubscriptions()
    {
        // Get all active finance applications with their customer info
        $activeFinanceApplications = FinanceApplication::getActiveFinanceApplications()
            ->with(['customer:id,first_name,last_name'])
            ->get();

        foreach ($activeFinanceApplications as $finance) {
            // Skip if weekly_instalment is 0 and deposit matches motorbike_price
            if ($finance->weekly_instalment == 0 && $finance->deposit == $finance->motorbike_price) {
                continue; // Skip fully paid finance applications
            }

            // Get the customer's onboarding record
            $onboarding = JudopayOnboarding::where('onboardable_type', Customer::class)
                ->where('onboardable_id', $finance->customer_id)
                ->first();

            if (! $onboarding) {
                continue; // Skip if no onboarding record
            }

            // Check if subscription already exists for this finance application
            $existingSubscription = JudopaySubscription::where('subscribable_type', FinanceApplication::class)
                ->where('subscribable_id', $finance->id)
                ->first();

            if ($existingSubscription) {
                continue; // Skip if subscription already exists
            }

            // Determine billing frequency and day
            $billingFrequency = $finance->is_monthly ? 'monthly' : 'weekly';

            if ($billingFrequency === 'weekly') {
                // Weekly finance is always Saturday
                $billingDay = 6; // Saturday (Carbon dayOfWeek: 1=Monday, 6=Saturday)
                $startDate = \Carbon\Carbon::parse($finance->contract_date)->next(6); // Next Saturday
            } else {
                // Monthly finance - use contract_date but ensure it's not 29, 30, or 31
                $contractDate = \Carbon\Carbon::parse($finance->contract_date);
                $dayOfMonth = $contractDate->day;

                // If day is 29, 30, or 31, use 28th instead
                if ($dayOfMonth >= 29) {
                    $dayOfMonth = 28;
                }

                $billingDay = $dayOfMonth;
                $startDate = $contractDate->copy()->day($dayOfMonth);
            }

            // Create consumer reference via helper
            $consumerReference = JudopayReference::buildConsumerReference(
                FinanceApplication::class,
                $finance,
                'NGNI-'.$finance->id.'-'.$finance->customer_id
            );

            JudopaySubscription::create([
                'judopay_onboarding_id' => $onboarding->id,
                'date' => now()->toDateString(),
                'subscribable_id' => $finance->id,
                'subscribable_type' => FinanceApplication::class,
                'billing_frequency' => $billingFrequency,
                'billing_day' => $billingDay,
                'amount' => $finance->weekly_instalment,
                'opening_balance' => 0, // Will be set based on finance application details
                'start_date' => $startDate,
                'end_date' => null, // Finance applications are ongoing until completed
                'status' => 'pending', // Default status
                'consumer_reference' => $consumerReference,
            ]);
        }
    }
}
