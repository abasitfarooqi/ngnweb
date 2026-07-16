<?php

namespace App\Support;

use App\Models\Motorbike;
use App\Models\MotorbikeAnnualCompliance;
use App\Models\MotorbikeRegistration;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use App\Models\RentingPricing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Shared “make bike selectable on New booking” repair — same rules as RentingController.
 */
class RentalAvailabilityRepair
{
    public function snapshot(int $motorbikeId): array
    {
        $items = RentingBookingItem::query()
            ->leftJoin('renting_bookings as rb', 'rb.id', '=', 'renting_booking_items.booking_id')
            ->where('renting_booking_items.motorbike_id', $motorbikeId)
            ->where('renting_booking_items.is_posted', true)
            ->whereNull('renting_booking_items.end_date')
            ->orderByDesc('renting_booking_items.id')
            ->get([
                'renting_booking_items.id as item_id',
                'renting_booking_items.booking_id',
                'renting_booking_items.is_posted',
                'renting_booking_items.start_date',
                'renting_booking_items.end_date',
                'rb.state as booking_state',
                'rb.is_posted as booking_is_posted',
            ])
            ->map(fn ($item) => [
                'item_id' => $item->item_id,
                'booking_id' => $item->booking_id,
                'is_posted' => (bool) $item->is_posted,
                'start_date' => $item->start_date,
                'end_date' => $item->end_date,
                'booking_state' => $item->booking_state,
                'booking_is_posted' => (bool) $item->booking_is_posted,
            ])
            ->values()
            ->all();

        return [
            'open_posted_items_count' => count($items),
            'items' => $items,
        ];
    }

    public function checks(int $motorbikeId): array
    {
        $motorbike = Motorbike::query()
            ->leftJoin('motorbike_annual_compliance as mac', 'mac.motorbike_id', '=', 'motorbikes.id')
            ->where('motorbikes.id', $motorbikeId)
            ->select(
                'motorbikes.id',
                'motorbikes.vehicle_profile_id',
                'motorbikes.is_ebike',
                'mac.mot_status',
                'mac.road_tax_status'
            )
            ->firstOrFail();

        $hasCurrentPricing = RentingPricing::where('motorbike_id', $motorbikeId)
            ->where('iscurrent', true)
            ->exists();
        $hasRegistration = MotorbikeRegistration::where('motorbike_id', $motorbikeId)->exists();
        $hasOpenPostedItem = RentingBookingItem::where('motorbike_id', $motorbikeId)
            ->where('is_posted', true)
            ->whereNull('end_date')
            ->exists();

        $vehicleProfileOk = (int) $motorbike->vehicle_profile_id === 1;
        $compliancePass = $motorbike->is_ebike
            ? true
            : ($motorbike->road_tax_status === 'Taxed'
                && in_array($motorbike->mot_status, ['Valid', 'No details held by DVLA'], true));

        $blockers = [];
        if (! $vehicleProfileOk) {
            $blockers[] = 'vehicle_profile_id must be 1 (internal)';
        }
        if (! $hasCurrentPricing) {
            $blockers[] = 'current pricing is missing';
        }
        if (! $hasRegistration) {
            $blockers[] = 'registration row is missing';
        }
        if ($hasOpenPostedItem) {
            $blockers[] = 'open posted booking item still exists';
        }
        if (! $compliancePass) {
            $blockers[] = 'MOT/tax compliance does not meet booking rules';
        }

        return [
            'vehicle_profile_ok' => $vehicleProfileOk,
            'has_current_pricing' => $hasCurrentPricing,
            'has_registration' => $hasRegistration,
            'has_open_posted_item' => $hasOpenPostedItem,
            'compliance_pass' => $compliancePass,
            'mot_status' => $motorbike->mot_status,
            'road_tax_status' => $motorbike->road_tax_status,
            'blockers' => $blockers,
            'is_selectable' => $vehicleProfileOk && $hasCurrentPricing && $hasRegistration && $compliancePass && ! $hasOpenPostedItem,
        ];
    }

    /**
     * Force-close open posted items, then repair missing profile / registration / pricing / compliance.
     *
     * @return array{items_closed:int,bookings_updated:int,repair_actions:array<int,string>,repair_errors:array<int,string>,checks:array,message:string}
     */
    public function execute(int $motorbikeId, ?int $auditUserId = null): array
    {
        $motorbike = Motorbike::findOrFail($motorbikeId);
        $snapshot = $this->snapshot($motorbikeId);
        $itemIds = collect($snapshot['items'])->pluck('item_id')->filter()->values();
        $bookingIds = collect($snapshot['items'])->pluck('booking_id')->filter()->unique()->values();

        $itemsClosed = 0;
        $bookingsUpdated = 0;

        DB::transaction(function () use ($itemIds, $bookingIds, &$itemsClosed, &$bookingsUpdated) {
            if ($itemIds->isNotEmpty()) {
                $itemsClosed = RentingBookingItem::whereIn('id', $itemIds)
                    ->update([
                        'end_date' => now(),
                        'is_posted' => false,
                        'updated_at' => now(),
                    ]);
            }

            foreach ($bookingIds as $bookingId) {
                $hasOpenPosted = RentingBookingItem::where('booking_id', $bookingId)
                    ->where('is_posted', true)
                    ->whereNull('end_date')
                    ->exists();

                if (! $hasOpenPosted) {
                    $bookingsUpdated += RentingBooking::where('id', $bookingId)
                        ->where('is_posted', true)
                        ->update([
                            'is_posted' => false,
                            'updated_at' => now(),
                        ]);
                }
            }
        });

        $repairActions = [];
        $repairErrors = [];
        try {
            $repairActions = $this->repairPrerequisites($motorbikeId, $auditUserId);
        } catch (\Throwable $e) {
            $repairErrors[] = $e->getMessage();
            Log::error('RentalAvailabilityRepair::repairPrerequisites failed: '.$e->getMessage());
        }

        $checks = $this->checks($motorbikeId);
        $message = $checks['is_selectable']
            ? 'Bike is now available for New booking.'
            : 'Repair ran, but blockers remain — check pricing/compliance.';

        Log::info('rental_availability_repair_executed', [
            'motorbike_id' => $motorbike->id,
            'reg_no' => $motorbike->reg_no,
            'audit_user_id' => $auditUserId,
            'items_closed' => $itemsClosed,
            'bookings_updated' => $bookingsUpdated,
            'repair_actions' => $repairActions,
            'remaining_blockers' => $checks['blockers'],
        ]);

        return [
            'items_closed' => $itemsClosed,
            'bookings_updated' => $bookingsUpdated,
            'repair_actions' => $repairActions,
            'repair_errors' => $repairErrors,
            'checks' => $checks,
            'message' => $message,
        ];
    }

    /** @return array<int, string> */
    public function repairPrerequisites(int $motorbikeId, ?int $auditUserId = null): array
    {
        $actions = [];
        $motorbike = Motorbike::findOrFail($motorbikeId);

        if ((int) $motorbike->vehicle_profile_id !== 1) {
            $motorbike->vehicle_profile_id = 1;
            $motorbike->save();
            $actions[] = 'set vehicle_profile_id to 1';
        }

        $hasRegistration = MotorbikeRegistration::where('motorbike_id', $motorbikeId)->exists();
        if (! $hasRegistration && ! empty($motorbike->reg_no)) {
            MotorbikeRegistration::create([
                'motorbike_id' => $motorbikeId,
                'registration_number' => $motorbike->reg_no,
                'active' => true,
                'start_date' => now()->toDateString(),
                'end_date' => now()->toDateString(),
            ]);
            $actions[] = 'created missing motorbike registration row';
        }

        $currentPricing = RentingPricing::where('motorbike_id', $motorbikeId)
            ->where('iscurrent', true)
            ->first();
        if (! $currentPricing) {
            $latestPricing = RentingPricing::where('motorbike_id', $motorbikeId)
                ->orderByDesc('id')
                ->first();

            RentingPricing::where('motorbike_id', $motorbikeId)->update(['iscurrent' => false]);

            if ($latestPricing) {
                $latestPricing->iscurrent = true;
                $latestPricing->update_date = now();
                $latestPricing->save();
                $actions[] = 'promoted latest pricing row as current';
            } else {
                RentingPricing::create([
                    'motorbike_id' => $motorbikeId,
                    'user_id' => $auditUserId,
                    'iscurrent' => true,
                    'weekly_price' => 70,
                    'minimum_deposit' => 0,
                    'update_date' => now(),
                ]);
                $actions[] = 'created fallback pricing row (£70)';
            }
        }

        if (! $motorbike->is_ebike) {
            $mac = MotorbikeAnnualCompliance::firstOrNew(['motorbike_id' => $motorbikeId]);
            $mac->year = $mac->year ?: (int) now()->format('Y');
            $mac->road_tax_status = 'Taxed';
            $mac->mot_status = 'No details held by DVLA';
            $mac->save();
            $actions[] = 'forced compliance to Taxed + No details held by DVLA';
        }

        return $actions;
    }
}
