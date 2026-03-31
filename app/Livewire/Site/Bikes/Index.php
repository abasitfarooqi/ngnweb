<?php

namespace App\Livewire\Site\Bikes;

use App\Models\Motorbike;
use App\Models\Motorcycle;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $filterType = 'all';

    public $searchMake = '';

    public $minPrice = '';

    public $maxPrice = '';

    public function setFilter($type)
    {
        $this->filterType = $type;
        $this->resetPage();
    }

    public function render()
    {
        $newBikes = collect();
        if (in_array($this->filterType, ['all', 'new'])) {
            try {
                $q = Motorcycle::where('availability', 'for sale');

                if ($this->searchMake) {
                    $q->where('make', 'like', '%'.$this->searchMake.'%');
                }

                $newBikes = $q
                    ->orderBy('created_at', 'desc')
                    ->get();
            } catch (\Exception $e) {
                $newBikes = collect();
            }
        }

        $usedBikes = collect();
        if (in_array($this->filterType, ['all', 'used'])) {
            try {
                $q = Motorbike::join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')
                    ->select(
                        'motorbikes.*',
                        'motorbikes_sale.price',
                        'motorbikes_sale.image_one',
                        'motorbikes_sale.mileage as sale_mileage',
                        'motorbikes_sale.id as sale_id',
                    )
                    ->where('motorbikes_sale.is_sold', 0);

                if ($this->searchMake) {
                    $q->where('motorbikes.make', 'like', '%'.$this->searchMake.'%');
                }

                if ($this->minPrice) {
                    $q->where('motorbikes_sale.price', '>=', $this->minPrice);
                }

                if ($this->maxPrice) {
                    $q->where('motorbikes_sale.price', '<=', $this->maxPrice);
                }

                $usedBikes = $q
                    ->orderBy('motorbikes.created_at', 'desc')
                    ->get();
            } catch (\Exception $e) {
                $usedBikes = collect();
            }
        }

        return view('livewire.site.bikes.index', compact('newBikes', 'usedBikes'))
            ->layout('components.layouts.public', [
                'title' => 'Used Motorcycles For Sale London | New Bikes | NGN Motors',
                'description' => 'Quality used motorcycles for sale in London. Finance available.',
            ]);
    }
}
