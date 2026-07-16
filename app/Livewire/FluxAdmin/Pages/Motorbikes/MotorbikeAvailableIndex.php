<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\Motorbike;
use App\Models\VehicleProfile;
use App\Support\RentalAvailabilityRepair;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Motorbike rental eligibility + one-click repair (profile / pricing / registration / compliance / stale bookings).
 * URL: /flux-admin/motorbike-available
 */
#[Layout('flux-admin.layouts.app')]
#[Title('Motorbike available — Flux Admin')]
class MotorbikeAvailableIndex extends Component
{
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public bool $showRepairForm = false;

    public ?int $repairId = null;

    public string $repairRegNo = '';

    /** @var array<string, mixed> */
    public array $repairPreview = [];

    /** @var array<string, mixed> */
    public array $repairChecks = [];

    public string $lastRepairMessage = '';

    public function mount(): void
    {
        if (! backpack_user()) {
            abort(403);
        }

        $this->exportable = true;
        $this->exportFilename = 'motorbike-available';
        $this->sortField = 'reg_no';
        $this->sortDirection = 'asc';
    }

    public function openRepair(int $id, RentalAvailabilityRepair $repair): void
    {
        $motorbike = Motorbike::query()->findOrFail($id);
        $this->repairId = $id;
        $this->repairRegNo = (string) ($motorbike->reg_no ?? '');
        $this->repairPreview = $repair->snapshot($id);
        $this->repairChecks = $repair->checks($id);
        $this->lastRepairMessage = '';
        $this->showRepairForm = true;
    }

    public function executeRepair(RentalAvailabilityRepair $repair): void
    {
        $id = $this->repairId;
        if (! $id) {
            return;
        }

        $result = $repair->execute($id, backpack_user()?->id);

        $this->repairPreview = $repair->snapshot($id);
        $this->repairChecks = $result['checks'];
        $this->lastRepairMessage = $result['message'];

        $detail = $result['message'];
        if ($result['repair_actions'] !== []) {
            $detail .= ' · '.implode('; ', $result['repair_actions']);
        }
        if ($result['items_closed'] > 0) {
            $detail .= ' · closed '.$result['items_closed'].' open item(s)';
        }

        $this->dispatch(
            'flux-admin:toast',
            type: $result['checks']['is_selectable'] ? 'success' : 'warning',
            message: $detail
        );

        if ($result['checks']['is_selectable']) {
            $this->showRepairForm = false;
            $this->repairId = null;
        }
    }

    public function render()
    {
        $sortCol = match ($this->sortField) {
            'vehicle_profile_id' => 'motorbikes.vehicle_profile_id',
            'weekly_price' => 'rp.weekly_price',
            'mot_status' => 'mac.mot_status',
            'road_tax_status' => 'mac.road_tax_status',
            default => 'motorbikes.reg_no',
        };

        $rows = $this->baseQuery()
            ->orderBy($sortCol, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.motorbikes.available-index', [
            'rows' => $rows,
            'profiles' => VehicleProfile::query()->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    protected function baseQuery(): Builder
    {
        $profileId = $this->filter('vehicle_profile_id');
        $motStatus = $this->filter('mot_status');
        $taxStatus = $this->filter('road_tax_status');
        $isCurrent = $this->filter('iscurrent');
        $isPosted = $this->filter('is_posted');
        $selectable = $this->filter('selectable');

        return Motorbike::query()
            ->select([
                'motorbikes.id',
                'motorbikes.reg_no',
                'motorbikes.make',
                'motorbikes.model',
                'motorbikes.vehicle_profile_id',
                'motorbikes.is_ebike',
                'mac.mot_status',
                'mac.road_tax_status',
                'rbi.end_date as booking_end_date',
                'rbi.is_posted as booking_is_posted',
                'rp.weekly_price',
                'rp.iscurrent',
            ])
            ->selectRaw('EXISTS(SELECT 1 FROM motorbike_registrations mr WHERE mr.motorbike_id = motorbikes.id) as has_registration')
            ->leftJoin('motorbike_annual_compliance as mac', 'mac.motorbike_id', '=', 'motorbikes.id')
            ->leftJoin('renting_pricings as rp', function ($join) {
                $join->on('rp.motorbike_id', '=', 'motorbikes.id')
                    ->where('rp.iscurrent', true);
            })
            ->leftJoin('renting_booking_items as rbi', function ($join) {
                $join->on('rbi.motorbike_id', '=', 'motorbikes.id')
                    ->where('rbi.is_posted', true)
                    ->whereNull('rbi.end_date');
            })
            ->when($this->search !== '', function ($q) {
                $term = '%'.trim($this->search).'%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('motorbikes.reg_no', 'like', $term)
                        ->orWhere('motorbikes.make', 'like', $term)
                        ->orWhere('motorbikes.model', 'like', $term);
                });
            })
            ->when(filled($profileId), fn ($q) => $q->where('motorbikes.vehicle_profile_id', $profileId))
            ->when(filled($motStatus), fn ($q) => $q->where('mac.mot_status', $motStatus))
            ->when(filled($taxStatus), fn ($q) => $q->where('mac.road_tax_status', $taxStatus))
            ->when(filled($isCurrent), fn ($q) => $q->where('rp.iscurrent', (bool) (int) $isCurrent))
            ->when(filled($isPosted), function ($q) use ($isPosted) {
                if ((string) $isPosted === '1') {
                    $q->where('rbi.is_posted', true);
                } else {
                    $q->where(function ($inner) {
                        $inner->whereNull('rbi.id')->orWhere('rbi.is_posted', false);
                    });
                }
            })
            ->when(filled($selectable), function ($q) use ($selectable) {
                // Same rules as New booking bike list (RentingController::bookingNewPageData).
                $eligible = function ($inner) {
                    $inner->where('motorbikes.vehicle_profile_id', 1)
                        ->whereNotNull('rp.id')
                        ->whereNull('rbi.id')
                        ->whereExists(function ($reg) {
                            $reg->selectRaw('1')
                                ->from('motorbike_registrations as mr')
                                ->whereColumn('mr.motorbike_id', 'motorbikes.id');
                        })
                        ->where(function ($bike) {
                            $bike->where('motorbikes.is_ebike', true)
                                ->orWhere(function ($ice) {
                                    $ice->where('motorbikes.is_ebike', false)
                                        ->where('mac.road_tax_status', 'Taxed')
                                        ->whereIn('mac.mot_status', ['Valid', 'No details held by DVLA']);
                                });
                        });
                };

                if ((string) $selectable === '1') {
                    $q->where($eligible);
                } else {
                    $q->whereNot(fn ($inner) => $eligible($inner));
                }
            });
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery();
    }

    protected function exportColumns(): array
    {
        return [
            'id' => 'ID',
            'reg_no' => 'Reg',
            'vehicle_profile_id' => 'Profile',
            'mot_status' => 'MOT',
            'road_tax_status' => 'Tax',
            'booking_end_date' => 'Booking end',
            'weekly_price' => 'Weekly £',
            'iscurrent' => 'Current price',
        ];
    }
}
