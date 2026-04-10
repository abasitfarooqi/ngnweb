<?php

namespace App\Livewire\Portal\Rentals;

use App\Models\Motorbike;
use Livewire\Component;

class Create extends Component
{
    public $motorbikeId;

    public $motorbike;

    public string $start_date = '';

    public int $rental_period = 1;

    public string $notes = '';

    public bool $agree_terms = false;

    public function mount($motorbikeId)
    {
        $this->motorbikeId = $motorbikeId;
        $this->motorbike = Motorbike::with(['images', 'currentRentingPricing', 'branch'])->find($motorbikeId);

        if (! $this->motorbike) {
            abort(404);
        }

        $this->start_date = now()->toDateString();
    }

    public function submit()
    {
        $this->validate(['agree_terms' => 'accepted']);
        session()->flash('success', 'Rental booking request submitted. We will contact you to confirm.');
        $this->redirectRoute('account.rentals.my-rentals');
    }

    public function createBooking()
    {
        $this->submit();
    }

    public function render()
    {
        $pricing = $this->motorbike->currentRentingPricing;
        $weekly_rent = $pricing ? (float) $pricing->weekly_price : 80.00;
        $deposit = $pricing ? (float) ($pricing->deposit ?? 200.00) : 200.00;
        $total_amount = $weekly_rent + $deposit;

        return view('livewire.portal.rentals.create', compact('weekly_rent', 'deposit', 'total_amount'))
            ->layout('components.layouts.portal', ['title' => 'Create Rental | My Account']);
    }
}
