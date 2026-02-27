<?php

namespace App\Livewire\V2;

use App\Models\MotorbikesSale;
use Livewire\Component;

class BikeDetail extends Component
{
    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render()
    {
        $bike = MotorbikesSale::with(['motorbike', 'motorbikeImage'])
            ->whereHas('motorbike', fn ($q) => $q->where('slug', $this->slug))
            ->firstOrFail();

        $related = MotorbikesSale::with(['motorbike', 'motorbikeImage'])
            ->where('is_sold', 0)
            ->where('id', '!=', $bike->id)
            ->latest()
            ->take(3)
            ->get();

        return view('livewire.v2.bike-detail', compact('bike', 'related'))
            ->layout('v2.layouts.app');
    }
}
