<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Motorbike;
use App\Models\RentingPricing;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Motorbike pricing — Flux Admin')]
class MotorbikePricingHub extends Component
{
    use WithAuthorization;

    private const PAGE_SIZE = 40;

    public ?int $selectedMotorbikeId = null;

    public string $selectedReg = '';

    public string $weeklyPrice = '';

    public string $minimumDeposit = '';

    public ?int $editingPricingId = null;

    public string $regSearch = '';

    public int $unpricedLimit = self::PAGE_SIZE;

    public int $currentLimit = self::PAGE_SIZE;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-rentals');
    }

    public function updatedRegSearch(): void
    {
        $this->unpricedLimit = self::PAGE_SIZE;
        $this->currentLimit = self::PAGE_SIZE;
    }

    public function loadMoreUnpriced(): void
    {
        $this->unpricedLimit += self::PAGE_SIZE;
    }

    public function loadMoreCurrent(): void
    {
        $this->currentLimit += self::PAGE_SIZE;
    }

    public function selectUnpriced(int $motorbikeId): void
    {
        $bike = Motorbike::find($motorbikeId);
        if (! $bike) {
            return;
        }

        $this->editingPricingId = null;
        $this->selectedMotorbikeId = $bike->id;
        $this->selectedReg = (string) ($bike->reg_no ?? '#'.$bike->id);
        $this->weeklyPrice = '';
        $this->minimumDeposit = '';
    }

    public function editCurrent(int $pricingId): void
    {
        $pricing = RentingPricing::with('motorbike:id,reg_no')->find($pricingId);
        if (! $pricing) {
            return;
        }

        $this->editingPricingId = $pricing->id;
        $this->selectedMotorbikeId = (int) $pricing->motorbike_id;
        $this->selectedReg = (string) ($pricing->motorbike?->reg_no ?? '#'.$pricing->motorbike_id);
        $this->weeklyPrice = (string) $pricing->weekly_price;
        $this->minimumDeposit = (string) $pricing->minimum_deposit;
    }

    public function savePricing(): void
    {
        $this->validate([
            'selectedMotorbikeId' => ['required', 'integer', 'exists:motorbikes,id'],
            'weeklyPrice' => ['required', 'numeric', 'min:0'],
            'minimumDeposit' => ['required', 'numeric', 'min:0'],
        ]);

        $weekly = (float) $this->weeklyPrice;
        $deposit = (float) $this->minimumDeposit;

        if ($this->editingPricingId && $weekly == 0.0 && $deposit == 0.0) {
            RentingPricing::whereKey($this->editingPricingId)->delete();
            $this->resetForm();
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Pricing deleted.');

            return;
        }

        DB::transaction(function () use ($weekly, $deposit) {
            if ($this->editingPricingId) {
                $existing = RentingPricing::findOrFail($this->editingPricingId);
                $existing->iscurrent = false;
                $existing->save();
            } else {
                RentingPricing::where('motorbike_id', $this->selectedMotorbikeId)
                    ->update(['iscurrent' => false]);
            }

            RentingPricing::create([
                'motorbike_id' => $this->selectedMotorbikeId,
                'weekly_price' => $weekly,
                'minimum_deposit' => $deposit,
                'iscurrent' => true,
                'user_id' => auth()->id(),
                'update_date' => now(),
            ]);
        });

        $this->resetForm();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Pricing saved.');
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingPricingId = null;
        $this->selectedMotorbikeId = null;
        $this->selectedReg = '';
        $this->weeklyPrice = '';
        $this->minimumDeposit = '';
    }

    public function render()
    {
        $search = trim($this->regSearch);

        $unpricedQuery = RentingPricing::unpricedMotorbikesQuery($search !== '' ? $search : null);
        $unpricedTotal = (clone $unpricedQuery)->count();
        $unpricedBatch = (clone $unpricedQuery)->limit($this->unpricedLimit + 1)->get();
        $hasMoreUnpriced = $unpricedBatch->count() > $this->unpricedLimit;
        $unpriced = $unpricedBatch->take($this->unpricedLimit);

        $currentQuery = RentingPricing::current()
            ->with('motorbike:id,reg_no,make,model')
            ->when($search !== '', fn ($q) => $q->whereHas(
                'motorbike',
                fn ($m) => RentingPricing::applyMotorbikeSearch($m, $search)
            ));
        $currentTotal = (clone $currentQuery)->count();
        $currentBatch = (clone $currentQuery)->limit($this->currentLimit + 1)->get();
        $hasMoreCurrent = $currentBatch->count() > $this->currentLimit;
        $current = $currentBatch->take($this->currentLimit);

        return view('flux-admin.pages.rentals.motorbike-pricing-hub', [
            'unpriced' => $unpriced,
            'unpricedTotal' => $unpricedTotal,
            'hasMoreUnpriced' => $hasMoreUnpriced,
            'current' => $current,
            'currentTotal' => $currentTotal,
            'hasMoreCurrent' => $hasMoreCurrent,
        ]);
    }
}
