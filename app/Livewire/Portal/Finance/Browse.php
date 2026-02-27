<?php

namespace App\Livewire\Portal\Finance;

use Livewire\Component;
use App\Models\Motorbike;

class Browse extends Component
{
    public function render()
    {
        $bikes = collect();
        try {
            $bikes = Motorbike::join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')
                ->select('motorbikes.*', 'motorbikes_sale.price', 'motorbikes_sale.image_one', 'motorbikes_sale.id as sale_id')
                ->where('motorbikes_sale.is_sold', 0)
                ->orderBy('motorbikes_sale.price')
                ->get();
        } catch (\Exception $e) {}

        return view('livewire.portal.finance.browse', compact('bikes'))
            ->layout('components.layouts.portal');
    }
}
