<?php

namespace App\Livewire\Site\SpareParts;

use App\Support\SpareParts\SparePartsCatalogue;
use Livewire\Component;

class Manufactures extends Component
{
    public function render()
    {
        $manufacturers = app(SparePartsCatalogue::class)->manufacturers();

        return view('livewire.site.spareparts.manufactures', compact('manufacturers'))
            ->layout('components.layouts.public', [
                'title' => 'Spareparts Manufacturers | NGN Motors',
                'description' => 'Browse spareparts manufacturers and start finding bike parts by fitment.',
            ]);
    }
}
