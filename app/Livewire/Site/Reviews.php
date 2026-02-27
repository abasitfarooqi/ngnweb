<?php

namespace App\Livewire\Site;

use Livewire\Component;

class Reviews extends Component
{
    public function render()
    {
        return view('livewire.site.reviews')
            ->layout('components.layouts.public', [
                'title' => 'Customer Reviews | NGN Motors',
                'description' => 'Read what our customers say about NGN Motors. Genuine reviews from satisfied customers across London.',
            ]);
    }
}
