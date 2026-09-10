<?php

namespace App\Services\Tracking;

use App\Models\Customer;
use App\Models\FinanceApplication;
use App\Models\RentingBooking;
use Illuminate\Database\Eloquent\Model;

final class TrackingAgreementService
{
    /** @return array<int, array{model: Model, vehicle_id: int}> */
    public function activeFor(Customer $customer): array
    {
        $contexts = [];

        RentingBooking::query()->where('customer_id', $customer->id)->active()->with('activeItems')->get()->each(function (RentingBooking $booking) use (&$contexts): void {
            foreach ($booking->activeItems as $item) {
                if ($item->motorbike_id) {
                    $contexts[] = ['model' => $booking, 'vehicle_id' => (int) $item->motorbike_id];
                }
            }
        });

        FinanceApplication::query()->where('customer_id', $customer->id)->activePaymentPlan()->with('items')->get()->each(function (FinanceApplication $application) use (&$contexts): void {
            foreach ($application->items as $item) {
                if ($item->motorbike_id) {
                    $contexts[] = ['model' => $application, 'vehicle_id' => (int) $item->motorbike_id];
                }
            }
        });

        return $contexts;
    }

    /** @return array{model: Model, vehicle_id: int}|null */
    public function findOwnedActive(Customer $customer, string $type, int $id): ?array
    {
        return collect($this->activeFor($customer))->first(function (array $context) use ($type, $id): bool {
            return match ($type) {
                'rental', 'renting_booking' => $context['model'] instanceof RentingBooking && (int) $context['model']->getKey() === $id,
                'finance', 'finance_application' => $context['model'] instanceof FinanceApplication && (int) $context['model']->getKey() === $id,
                default => false,
            };
        });
    }
}
