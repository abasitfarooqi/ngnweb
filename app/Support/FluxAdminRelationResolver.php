<?php

namespace App\Support;

use App\Models\Customer;
use App\Models\FinanceApplication;
use App\Models\PcnCase;
use App\Models\RentingBooking;
use Illuminate\Support\Collection;

class FluxAdminRelationResolver
{
    /** @return Collection<int, RentingBooking> */
    public function rentalsForCustomer(int $customerId, int $limit = 10): Collection
    {
        return RentingBooking::query()
            ->where('customer_id', $customerId)
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    /** @return Collection<int, FinanceApplication> */
    public function financeForCustomer(int $customerId, int $limit = 10): Collection
    {
        return FinanceApplication::query()
            ->where('customer_id', $customerId)
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    /** @return Collection<int, PcnCase> */
    public function pcnForCustomer(int $customerId, int $limit = 10): Collection
    {
        return PcnCase::query()
            ->where('customer_id', $customerId)
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    /** @return Collection<int, PcnCase> */
    public function pcnForMotorbike(?int $motorbikeId, int $limit = 10): Collection
    {
        if (! $motorbikeId) {
            return collect();
        }

        return PcnCase::query()
            ->where('motorbike_id', $motorbikeId)
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function customerForBooking(RentingBooking $booking): ?Customer
    {
        return $booking->customer;
    }

    public function customerRoute(?Customer $customer): ?string
    {
        return $customer ? route('flux-admin.customers.show', $customer->id) : null;
    }

    public function rentalRoute(RentingBooking $booking): string
    {
        return route('flux-admin.rentals.show', $booking->id);
    }

    public function financeRoute(FinanceApplication $application): string
    {
        return route('flux-admin.finance.show', $application->id);
    }

    public function pcnRoute(PcnCase $case): string
    {
        return route('flux-admin.pcn.show', $case->id);
    }
}
