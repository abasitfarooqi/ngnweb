<?php

namespace App\Livewire\Site\SpareParts;

use App\Models\SpAssembly;
use Livewire\Component;

class Categories extends Component
{
    public function render()
    {
        $categories = SpAssembly::query()
            ->selectRaw('name, COUNT(*) as count_rows')
            ->groupBy('name')
            ->orderBy('name')
            ->get();

        return view('livewire.site.spareparts.categories', compact('categories'))
            ->layout('components.layouts.public', [
                'title' => 'Spareparts Categories | NGN Motors',
                'description' => 'Browse spareparts categories and assemblies.',
            ]);
    }
}
