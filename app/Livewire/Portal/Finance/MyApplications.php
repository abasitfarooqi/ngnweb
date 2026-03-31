<?php

namespace App\Livewire\Portal\Finance;

use App\Models\FinanceApplication;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyApplications extends Component
{
    protected function subscriptionOptions(): array
    {
        return [
            'A' => ['label' => 'Group A - £299.99/month', 'amount' => 299.99],
            'B' => ['label' => 'Group B - £399.99/month', 'amount' => 399.99],
            'C' => ['label' => 'Group C - £549.99/month', 'amount' => 549.99],
            'D' => ['label' => 'Group D - £649.99/month', 'amount' => 649.99],
        ];
    }

    protected function resolveStatus(FinanceApplication $application): array
    {
        if ((bool) $application->is_cancelled) {
            return [
                'label' => 'Inactive',
                'class' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200',
            ];
        }

        if ((bool) $application->log_book_sent) {
            return [
                'label' => 'Completed',
                'class' => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200',
            ];
        }

        if ((bool) $application->is_posted) {
            return [
                'label' => 'Active',
                'class' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
            ];
        }

        return [
            'label' => 'Pending Review',
            'class' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200',
        ];
    }

    protected function deriveFinanceSnapshot(FinanceApplication $application): array
    {
        $subscriptionMap = $this->subscriptionOptions();
        $selectedSubscription = null;

        if ((bool) $application->is_subscription && ! empty($application->subscription_option)) {
            $selectedSubscription = $subscriptionMap[$application->subscription_option] ?? null;
        }

        $principal = max(0, (float) $application->motorbike_price - (float) $application->deposit);
        $extra = max(0, (float) ($application->extra ?? 0));
        $instalment = max(0.0, (float) $application->weekly_instalment);
        $totalMonths = 0;

        if ($selectedSubscription) {
            $instalment = (float) $selectedSubscription['amount'];
            $totalMonths = 12;
            $financedTotal = $instalment * $totalMonths;
        } else {
            $financedTotal = $principal + $extra;
            if ($instalment > 0) {
                $totalMonths = (int) ceil($financedTotal / $instalment);
            }
        }

        $monthsPassed = 0;
        if (! empty($application->first_instalment_date)) {
            try {
                $start = Carbon::parse($application->first_instalment_date)->startOfDay();
                $today = Carbon::now()->startOfDay();
                if ($today->greaterThanOrEqualTo($start)) {
                    // Include the current month only when we have reached the start date.
                    $monthsPassed = $start->diffInMonths($today) + 1;
                }
            } catch (\Throwable $e) {
                $monthsPassed = 0;
            }
        }

        if ($totalMonths > 0) {
            $monthsPassed = min($monthsPassed, $totalMonths);
        }

        $totalPaid = $monthsPassed * $instalment;
        $remainingBalance = max(0, $financedTotal - $totalPaid);

        return [
            'principal' => $principal,
            'extra' => $extra,
            'financed_total' => $financedTotal,
            'instalment' => $instalment,
            'total_months' => $totalMonths,
            'months_passed' => $monthsPassed,
            'total_paid' => $totalPaid,
            'remaining_balance' => $remainingBalance,
            'subscription_label' => $selectedSubscription['label'] ?? null,
        ];
    }

    public function render()
    {
        $customerAuth = Auth::guard('customer')->user();
        $customer = $customerAuth?->customer;
        $applications = collect();

        if ($customer) {
            try {
                $applications = FinanceApplication::where('customer_id', $customer->id)
                    ->with(['items.motorbike', 'customerContracts'])
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function (FinanceApplication $application) {
                        $application->portal_snapshot = $this->deriveFinanceSnapshot($application);
                        $application->portal_status = $this->resolveStatus($application);

                        return $application;
                    });
            } catch (\Exception $e) {
                $applications = collect();
            }
        }

        return view('livewire.portal.finance.my-applications', compact('applications'))
            ->layout('components.layouts.portal', ['title' => 'Finance Applications | My Account']);
    }
}
