<?php

namespace App\Livewire\Site\Rentals;

use App\Models\Motorbike;
use Livewire\Component;

class Show extends Component
{
    public $motorbike;
    public $pricing;
    public $selectedPeriod = 'weekly';

    public function mount($id)
    {
        try {
            $this->motorbike = Motorbike::with(['images', 'currentRentingPricing', 'branch'])
                ->findOrFail($id);
            $this->pricing = $this->motorbike->currentRentingPricing;
        } catch (\Exception $e) {
            abort(404, 'Motorcycle not found');
        }
    }

    public function setPeriod($period)
    {
        $this->selectedPeriod = $period;
    }

    public function calculatePrice()
    {
        if (!$this->pricing) return 80;
        return match($this->selectedPeriod) {
            'daily'   => round($this->pricing->weekly_price / 6, 2),
            'weekly'  => $this->pricing->weekly_price,
            'monthly' => round($this->pricing->weekly_price * 4 * 0.9, 2),
            default   => $this->pricing->weekly_price,
        };
    }

    public function render()
    {
        return view('livewire.site.rentals.show')
            ->layout('components.layouts.public', [
                'title' => $this->motorbike->make . ' ' . $this->motorbike->model . ' - Motorcycle Rental | NGN Motors',
                'description' => 'Rent ' . $this->motorbike->make . ' ' . $this->motorbike->model . ' from £' . $this->calculatePrice() . '/' . $this->selectedPeriod . '.',
            ]);
    }
}
