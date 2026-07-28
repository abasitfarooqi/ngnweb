<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\Branch;
use App\Models\Motorbike;
use App\Models\MotorbikeAnnualCompliance;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use App\Models\RentingPricing;
use App\Support\MotorbikeDeletion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Motorbikes — Flux Admin')]
class MotorbikeIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithExport, WithPagination;

    public bool $showForm = false;

    public string $branch = '';

    public string $filterColour = '';

    public string $filterYear = '';

    public string $filterMotStatus = '';

    public string $filterType = '';

    public bool $showMakeAvailableForm = false;

    public ?int $makeAvailableId = null;

    public string $maRegNo = '';

    /** Preview of open rental links before confirming execute */
    public array $maPreview = [];

    public ?string $deleteError = null;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->sortField = 'reg_no';
        $this->sortDirection = 'asc';
        $this->exportFilename = 'motorbikes';
        $this->exportable = true;
    }

    protected function formModel(): string
    {
        return Motorbike::class;
    }

    protected function formRules(): array
    {
        return [
            'formData.reg_no'     => ['required', 'string', 'max:20'],
            'formData.make'       => ['required', 'string', 'max:100'],
            'formData.model'      => ['required', 'string', 'max:100'],
            'formData.year'       => ['nullable', 'string', 'max:4'],
            'formData.vin_number' => ['nullable', 'string', 'max:50'],
            'formData.engine'     => ['nullable', 'string', 'max:50'],
            'formData.color'      => ['nullable', 'string', 'max:50'],
            'formData.fuel_type'  => ['nullable', 'string', 'max:100'],
            'formData.is_ebike'   => ['boolean'],
            'formData.branch_id'  => ['nullable', 'integer', 'exists:branches,id'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['is_ebike' => false];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(Motorbike::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Saved.');
    }

    public function delete(int $id): void
    {
        $this->deleteError = null;

        try {
            MotorbikeDeletion::delete(Motorbike::findOrFail($id));
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Motorbike deleted.');
        } catch (\Throwable $e) {
            $this->deleteError = $e->getMessage();
            $this->dispatch('flux-admin:toast', type: 'error', heading: 'Delete failed', message: $e->getMessage());
        }
    }

    public function dismissDeleteError(): void
    {
        $this->deleteError = null;
    }

    public function openMakeAvailable(int $id): void
    {
        $motorbike = Motorbike::findOrFail($id);

        $openItems = RentingBookingItem::query()
            ->leftJoin('renting_bookings as rb', 'rb.id', '=', 'renting_booking_items.booking_id')
            ->where('renting_booking_items.motorbike_id', $id)
            ->where('renting_booking_items.is_posted', true)
            ->whereNull('renting_booking_items.end_date')
            ->get([
                'renting_booking_items.id as item_id',
                'renting_booking_items.booking_id',
                'rb.state as booking_state',
                'renting_booking_items.start_date',
            ])
            ->map(fn ($i) => [
                'item_id'       => $i->item_id,
                'booking_id'    => $i->booking_id,
                'booking_state' => $i->booking_state,
                'start_date'    => $i->start_date,
            ])
            ->values()
            ->all();

        $this->makeAvailableId = $id;
        $this->maRegNo = $motorbike->reg_no ?? '';
        $this->maPreview = [
            'open_items_count'    => count($openItems),
            'open_items'          => $openItems,
            'has_current_pricing' => RentingPricing::where('motorbike_id', $id)->where('iscurrent', true)->exists(),
        ];
        $this->showMakeAvailableForm = true;
    }

    public function executeMakeAvailable(): void
    {
        $id = $this->makeAvailableId;

        DB::transaction(function () use ($id) {
            $itemIds = RentingBookingItem::where('motorbike_id', $id)
                ->where('is_posted', true)
                ->whereNull('end_date')
                ->pluck('id');

            if ($itemIds->isNotEmpty()) {
                $bookingIds = RentingBookingItem::whereIn('id', $itemIds)->pluck('booking_id')->unique();

                RentingBookingItem::whereIn('id', $itemIds)->update([
                    'end_date'   => now(),
                    'is_posted'  => false,
                    'updated_at' => now(),
                ]);

                foreach ($bookingIds as $bookingId) {
                    $hasOtherOpen = RentingBookingItem::where('booking_id', $bookingId)
                        ->where('is_posted', true)
                        ->whereNull('end_date')
                        ->exists();
                    if (! $hasOtherOpen) {
                        RentingBooking::where('id', $bookingId)->where('is_posted', true)
                            ->update(['is_posted' => false, 'updated_at' => now()]);
                    }
                }
            }
        });

        $this->showMakeAvailableForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Motorbike made available — active rental links closed.');
    }

    public function updatingBranch(): void { $this->resetPage(); }

    public function updatingFilterColour(): void { $this->resetPage(); }

    public function updatingFilterYear(): void { $this->resetPage(); }

    public function updatingFilterMotStatus(): void { $this->resetPage(); }

    public function updatingFilterType(): void { $this->resetPage(); }

    public function render()
    {
        $motorbikes = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.motorbikes.index', [
            'motorbikes' => $motorbikes,
            'branches'   => Branch::orderBy('name')->get(),
        ]);
    }

    protected function baseQuery(): Builder
    {
        return Motorbike::with(['vehicleProfile', 'branch', 'latestCompliance'])
            ->withCount('annualCompliances', 'repairs', 'rentingBookingItems')
            ->when($this->search !== '', function ($q) {
                $q->where(function ($q) {
                    $q->where('reg_no', 'like', "%{$this->search}%")
                        ->orWhere('make', 'like', "%{$this->search}%")
                        ->orWhere('model', 'like', "%{$this->search}%")
                        ->orWhere('vin_number', 'like', "%{$this->search}%");
                });
            })
            ->when($this->branch !== '', fn ($q) => $q->where('branch_id', $this->branch))
            ->when($this->filterColour !== '', fn ($q) => $q->where('color', 'like', "%{$this->filterColour}%"))
            ->when($this->filterYear !== '', fn ($q) => $q->where('year', $this->filterYear))
            ->when($this->filterMotStatus === '1', fn ($q) => $q->whereHas('annualCompliances'))
            ->when($this->filterMotStatus === '0', fn ($q) => $q->whereDoesntHave('annualCompliances'))
            ->when($this->filterType !== '', fn ($q) => $q->where('vehicle_profile_id', $this->filterType));
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery();
    }

    protected function exportColumns(): array
    {
        return [
            'ID'           => 'id',
            'Reg No'       => 'reg_no',
            'Make'         => 'make',
            'Model'        => 'model',
            'Colour'       => 'color',
            'Year'         => 'year',
            'Status'       => fn ($r) => $r->vehicleProfile?->name ?? '',
            'MOT Due'      => fn ($r) => $r->latestCompliance?->mot_due_date ?? '',
            'Road Tax Due' => fn ($r) => $r->latestCompliance?->tax_due_date ?? '',
            'Branch'       => fn ($r) => $r->branch?->name ?? '',
        ];
    }
}
