<?php

namespace App\Livewire\Site\Bikes;

use App\Models\Motorcycle;
use Livewire\Component;

class SalesIndex extends Component
{
    public string $search = '';

    public string $sort = 'default';

    public function mount(): void
    {
        $this->search = (string) request()->query('search', '');
        $this->sort = (string) request()->query('sort', 'default');
    }

    public function render()
    {
        $query = Motorcycle::query()->where('availability', 'for sale');

        if ($this->search !== '') {
            $query->where(function ($inner) {
                $inner->where('make', 'like', '%'.$this->search.'%')
                    ->orWhere('model', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->sort === 'price_asc') {
            $query->orderByRaw('COALESCE(sale_new_price, 0) asc');
        } elseif ($this->sort === 'price_desc') {
            $query->orderByRaw('COALESCE(sale_new_price, 0) desc');
        } else {
            $query->orderByDesc('id');
        }

        $motorcycles = $query->get();

        return view('livewire.site.bikes.sales-index', compact('motorcycles'))
            ->layout('components.layouts.public', [
                'title' => 'Motorcycles For Sale - New and Used Motorcycles for Sale in London, Catford, Tooting and Sutton',
                'description' => 'Browse motorcycles for sale with NGN Motors and enquire online.',
            ]);
    }
}
