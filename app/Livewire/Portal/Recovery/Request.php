<?php

namespace App\Livewire\Portal\Recovery;

use Livewire\Component;

class Request extends Component
{
    public $location = '';
    public $regNo = '';
    public $make = '';
    public $model = '';
    public $description = '';
    public $phone = '';

    public function submit()
    {
        $this->validate([
            'location'    => 'required|string|min:5',
            'regNo'       => 'required|string|min:2',
            'phone'       => 'required|string|min:10',
            'description' => 'required|string|min:10',
        ]);
        session()->flash('success', 'Recovery request submitted. We will contact you shortly.');
        $this->reset(['location', 'regNo', 'make', 'model', 'description', 'phone']);
    }

    public function render()
    {
        return view('livewire.portal.recovery.request')
            ->layout('components.layouts.portal');
    }
}
