<?php

namespace App\Livewire\Site;

use App\Models\Branch;
use App\Models\Motorbike;
use App\Models\Motorcycle;
use Livewire\Component;

class Home extends Component
{
    public $branches;
    public $featuredRentals = [];
    public $newBikesForSale = [];
    public $usedBikesForSale = [];

    public function mount()
    {
        $this->branches = Branch::orderBy('name')->get();

        $this->featuredRentals = Motorbike::whereHas('rentingPricings')
            ->with(['currentRentingPricing', 'images'])
            ->take(4)
            ->get();

        try {
            $this->newBikesForSale = Motorcycle::where('availability', 'for sale')
                ->orderBy('created_at', 'desc')
                ->limit(4)
                ->get();
        } catch (\Exception $e) {
            $this->newBikesForSale = collect();
        }

        try {
            $this->usedBikesForSale = Motorbike::join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')
                ->select('motorbikes.*', 'motorbikes_sale.price', 'motorbikes_sale.image_one')
                ->where('motorbikes_sale.is_sold', 0)
                ->orderBy('motorbikes.created_at', 'desc')
                ->limit(4)
                ->get();
        } catch (\Exception $e) {
            $this->usedBikesForSale = collect();
        }
    }

    public function render()
    {
        return view('livewire.site.home')
            ->layout('components.layouts.public', [
                'title' => 'NGN Motors - Motorcycle Rentals, MOT, Repairs & Sales in London',
                'description' => 'Expert motorcycle services in London. Rentals from £80/week, MOT testing, repairs, servicing, used bikes, and finance.',
            ]);
    }
}
