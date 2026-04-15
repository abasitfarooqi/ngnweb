<?php

namespace App\Livewire\FluxAdmin\Partials\Motorbikes;

use App\Models\MotorbikeImage;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class ImagesTab extends Component
{
    public int $motorbikeId;

    public function placeholder(): string
    {
        return view('flux-admin.partials.loading-placeholder')->render();
    }

    public function render()
    {
        $images = MotorbikeImage::where('motorbike_id', $this->motorbikeId)->get();

        return view('flux-admin.partials.motorbikes.images-tab', [
            'images' => $images,
        ]);
    }
}
