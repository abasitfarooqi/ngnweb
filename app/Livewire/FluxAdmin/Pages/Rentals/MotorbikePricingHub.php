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

    public ?int $selectedMotorbikeId = null;

    public string $selectedReg = '';

    public string $weeklyPrice = '';

    public string $minimumDeposit = '';

    public ?int $editingPricingId = null;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-rentals');
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
        return view('flux-admin.pages.rentals.motorbike-pricing-hub', [
            'unpriced' => RentingPricing::motorbikeNotPriced(),
            'current' => RentingPricing::current()->with('motorbike:id,reg_no,make,model')->get(),
        ]);
    }
}
