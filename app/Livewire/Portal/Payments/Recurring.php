<?php

namespace App\Livewire\Portal\Payments;

use App\Models\Customer;
use App\Models\FinanceApplication;
use App\Models\JudopayOnboarding;
use App\Models\JudopaySubscription;
use App\Models\NgnMitQueue;
use App\Models\RentingBooking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Recurring extends Component
{
    public string $serviceFilter = 'all';

    public function setServiceFilter(string $service): void
    {
        if (! in_array($service, ['all', 'rental', 'finance'], true)) {
            return;
        }

        $this->serviceFilter = $service;
    }

    protected function resolveCustomer(): ?Customer
    {
        return Auth::guard('customer')->user()?->customer;
    }

    protected function resolveSubscriptionCollection(?Customer $customer): Collection
    {
        if (! $customer) {
            return collect();
        }

        $onboardingIds = JudopayOnboarding::query()
            ->where('onboardable_type', Customer::class)
            ->where('onboardable_id', $customer->id)
            ->pluck('id');

        $rentalIds = RentingBooking::query()
            ->where('customer_id', $customer->id)
            ->pluck('id');

        $financeIds = FinanceApplication::query()
            ->where('customer_id', $customer->id)
            ->pluck('id');

        if ($onboardingIds->isEmpty() && $rentalIds->isEmpty() && $financeIds->isEmpty()) {
            return collect();
        }

        return JudopaySubscription::query()
            ->with([
                'subscribable' => function (MorphTo $morphTo): void {
                    $morphTo->morphWith([
                        RentingBooking::class => ['rentingBookingItems.motorbike'],
                        FinanceApplication::class => ['application_items.motorbike'],
                    ]);
                },
                'mitPaymentSessions',
                'citPaymentSessions',
            ])
            ->where(function (Builder $query) use ($onboardingIds, $rentalIds, $financeIds): void {
                if ($onboardingIds->isNotEmpty()) {
                    $query->orWhereIn('judopay_onboarding_id', $onboardingIds);
                }

                if ($rentalIds->isNotEmpty()) {
                    $query->orWhere(function (Builder $rentalQuery) use ($rentalIds): void {
                        $rentalQuery
                            ->where('subscribable_type', RentingBooking::class)
                            ->whereIn('subscribable_id', $rentalIds);
                    });
                }

                if ($financeIds->isNotEmpty()) {
                    $query->orWhere(function (Builder $financeQuery) use ($financeIds): void {
                        $financeQuery
                            ->where('subscribable_type', FinanceApplication::class)
                            ->whereIn('subscribable_id', $financeIds);
                    });
                }
            })
            ->orderByDesc('id')
            ->get()
            ->map(function (JudopaySubscription $subscription) {
                $mitSessions = $subscription->mitPaymentSessions->sortByDesc('created_at')->values();
                $successful = $mitSessions->where('status', 'success');
                $failed = $mitSessions->filter(fn ($row) => in_array($row->status, ['declined', 'error', 'cancelled'], true));
                $queued = NgnMitQueue::query()
                    ->where('subscribable_id', $subscription->id)
                    ->where('cleared', false)
                    ->orderBy('mit_fire_date')
                    ->get();

                $serviceType = $subscription->subscribable_type === RentingBooking::class ? 'rental' : 'finance';
                $serviceLabel = $serviceType === 'rental' ? 'Rental' : 'Finance';

                $vehicleLabel = 'N/A';
                if ($serviceType === 'rental') {
                    $item = $subscription->subscribable?->rentingBookingItems?->first();
                    $bike = $item?->motorbike;
                    if ($bike) {
                        $vehicleLabel = trim(($bike->make ?? '').' '.($bike->model ?? '').' '.($bike->reg_no ?? ''));
                    }
                } else {
                    $item = $subscription->subscribable?->application_items?->first();
                    $bike = $item?->motorbike;
                    if ($bike) {
                        $vehicleLabel = trim(($bike->make ?? '').' '.($bike->model ?? '').' '.($bike->reg_no ?? ''));
                    }
                }

                $lastPaid = $successful->first();
                $citApproved = $subscription->citPaymentSessions
                    ->where('status', 'success')
                    ->sortByDesc('payment_completed_at')
                    ->first();

                $subscription->portal_payment_summary = [
                    'service_type' => $serviceType,
                    'service_label' => $serviceLabel,
                    'vehicle_label' => $vehicleLabel,
                    'paid_total' => (float) $successful->sum('amount'),
                    'paid_count' => $successful->count(),
                    'failed_count' => $failed->count(),
                    'queued_count' => $queued->count(),
                    'next_due_at' => $queued->first()?->mit_fire_date,
                    'last_paid_at' => $lastPaid?->payment_completed_at ?: $lastPaid?->created_at,
                    'cit_approved_at' => $citApproved?->payment_completed_at,
                ];

                return $subscription;
            })
            ->filter(function (JudopaySubscription $subscription): bool {
                $serviceType = $subscription->portal_payment_summary['service_type'] ?? 'finance';

                return $this->serviceFilter === 'all' ? true : $serviceType === $this->serviceFilter;
            })
            ->values();
    }

    public function render()
    {
        $subscriptions = $this->resolveSubscriptionCollection($this->resolveCustomer());

        return view('livewire.portal.payments.recurring', compact('subscriptions'))
            ->layout('components.layouts.portal', ['title' => 'Recurring Payments | My Account']);
    }
}
