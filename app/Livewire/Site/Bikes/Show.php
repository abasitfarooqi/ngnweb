<?php

namespace App\Livewire\Site\Bikes;

use App\Models\Motorbike;
use App\Models\Motorcycle;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Show extends Component
{
    public $bike;
    public $isNew = false;
    public $saleInfo = null;

    public function mount($type, $id)
    {
        $this->isNew = ($type === 'new');

        if ($this->isNew) {
            try {
                $this->bike = Motorcycle::findOrFail($id);
            } catch (\Exception $e) {
                abort(404, 'Motorcycle not found');
            }
        } else {
            try {
                $this->bike = Motorbike::with(['images', 'branch'])->findOrFail($id);
                $this->saleInfo = DB::table('motorbikes_sale')
                    ->where('motorbike_id', $id)
                    ->where('is_sold', 0)
                    ->first();
            } catch (\Exception $e) {
                abort(404, 'Motorcycle not found');
            }
        }
    }

    public function render()
    {
        return view('livewire.site.bikes.show')
            ->layout('components.layouts.public', [
                'title' => ($this->bike->make ?? '') . ' ' . ($this->bike->model ?? '') . ' For Sale | NGN Motors',
                'description' => 'Buy ' . ($this->bike->make ?? '') . ' ' . ($this->bike->model ?? '') . ' at NGN Motors London. Finance available.',
            ]);
    }
}
