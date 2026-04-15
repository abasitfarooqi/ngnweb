<?php

namespace App\Livewire\FluxAdmin\Partials\Motorbikes;

use App\Models\RentingPricing;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class PricingTab extends Component
{
    public int $motorbikeId;

    public function placeholder(): string
    {
        return view('flux-admin.partials.loading-placeholder')->render();
    }

    public function render()
    {
        $current = RentingPricing::where('motorbike_id', $this->motorbikeId)
            ->Current()
            ->first();

        $history = RentingPricing::where('motorbike_id', $this->motorbikeId)
            ->Old()
            ->orderByDesc('update_date')
            ->get();

        return view('flux-admin.partials.motorbikes.pricing-tab', [
            'current' => $current,
            'history' => $history,
        ]);
    }
}
