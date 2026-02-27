<?php

namespace App\Livewire\V2;

use App\Models\MotorbikesSale;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BikesForSale extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $sort = 'latest';

    #[Url]
    public string $condition = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCondition(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = MotorbikesSale::with(['motorbike', 'motorbikeImage'])
            ->where('is_sold', 0);

        if ($this->search) {
            $query->whereHas('motorbike', function ($q) {
                $q->where('make', 'like', '%'.$this->search.'%')
                    ->orWhere('model', 'like', '%'.$this->search.'%')
                    ->orWhere('reg_no', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->condition) {
            $query->where('condition', $this->condition);
        }

        $query->when($this->sort === 'price_asc', fn ($q) => $q->orderBy('price', 'asc'))
              ->when($this->sort === 'price_desc', fn ($q) => $q->orderBy('price', 'desc'))
              ->when($this->sort === 'latest', fn ($q) => $q->latest());

        $bikes = $query->paginate(12);

        return view('livewire.v2.bikes-for-sale', compact('bikes'))
            ->layout('v2.layouts.app');
    }
}
