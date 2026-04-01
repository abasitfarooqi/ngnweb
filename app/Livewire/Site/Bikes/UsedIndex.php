<?php

namespace App\Livewire\Site\Bikes;

use App\Models\Motorbike;
use App\Models\Motorcycle;
use Livewire\Component;

class UsedIndex extends Component
{
    public string $search = '';

    public string $sort = 'default';

    public string $availability = 'available';

    public function mount(): void
    {
        $this->search = (string) request()->query('search', '');
        $this->sort = (string) request()->query('sort', 'default');
        $this->availability = (string) request()->query('availability', 'available');
    }

    public function render()
    {
        // Core list matches Home::usedBikesForSale (join + select + is_sold = 0), without limit.
        $query = Motorbike::query()
            ->join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')
            ->select(
                'motorbikes.*',
                'motorbikes_sale.price',
                'motorbikes_sale.image_one',
                'motorbikes_sale.mileage as sale_mileage',
                'motorbikes_sale.is_sold',
            );

        if ($this->search !== '') {
            $query->where(function ($inner) {
                $inner->where('motorbikes.make', 'like', '%'.$this->search.'%')
                    ->orWhere('motorbikes.model', 'like', '%'.$this->search.'%')
                    ->orWhere('motorbikes.reg_no', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->availability === 'sold') {
            $query->where('motorbikes_sale.is_sold', 1);
        } else {
            $query->where('motorbikes_sale.is_sold', 0);
        }

        if ($this->sort === 'price_asc') {
            $query->orderBy('motorbikes_sale.price');
        } elseif ($this->sort === 'price_desc') {
            $query->orderByDesc('motorbikes_sale.price');
        } elseif ($this->sort === 'year_asc') {
            $query->orderBy('motorbikes.year');
        } elseif ($this->sort === 'year_desc') {
            $query->orderByDesc('motorbikes.year');
        } else {
            $query->orderBy('motorbikes.created_at', 'desc');
        }

        $motorbikes = $query->get();

        $latestMotorcycles = Motorcycle::query()
            ->where('availability', 'for sale')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('livewire.site.bikes.used-index', compact('motorbikes', 'latestMotorcycles'))
            ->layout('components.layouts.public', [
                'title' => 'Used Motorbike For Sale - NGN - Motorcycle Rentals, Repairs, Accessories in Catford, Tooting, UK',
                'description' => 'Browse used motorcycles for sale with full details, enquiry and finance links.',
            ]);
    }
}
