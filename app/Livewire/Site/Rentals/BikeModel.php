<?php

namespace App\Livewire\Site\Rentals;

use App\Models\Motorbike;
use Livewire\Component;

class BikeModel extends Component
{
    public $modelSlug;
    public $make;
    public $model;
    public $bikes;

    public function mount($slug)
    {
        $this->modelSlug = $slug;

        $mapping = [
            'honda-forza-125'  => ['Honda', 'Forza 125'],
            'honda-pcx-125'    => ['Honda', 'PCX 125'],
            'honda-sh-125'     => ['Honda', 'SH 125'],
            'honda-vision-125' => ['Honda', 'Vision 125'],
            'yamaha-nmax-125'  => ['Yamaha', 'NMAX 125'],
            'yamaha-xmax-125'  => ['Yamaha', 'XMAX 125'],
        ];

        if (isset($mapping[$slug])) {
            [$this->make, $this->model] = $mapping[$slug];

            $this->bikes = Motorbike::where('make', 'like', '%' . $this->make . '%')
                ->where('model', 'like', '%' . str_replace(' ', '%', $this->model) . '%')
                ->whereHas('rentingPricings')
                ->with(['currentRentingPricing', 'images', 'branch'])
                ->get();
        } else {
            abort(404);
        }
    }

    public function render()
    {
        return view('livewire.site.rentals.bike-model')
            ->layout('components.layouts.public', [
                'title' => $this->make . ' ' . $this->model . ' Rental London | NGN Motors',
                'description' => 'Rent ' . $this->make . ' ' . $this->model . ' in London from NGN Motors. Flexible daily, weekly & monthly rates.',
            ]);
    }
}
