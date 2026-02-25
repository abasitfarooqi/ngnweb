<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ServiceBooking extends Component
{
    public $name = '';

    public $email = '';

    public $phone = '';

    public $typeOfService = '';

    public $message = '';

    public $vrm = '';

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'phone' => 'required',
        'typeOfService' => 'required',
        'vrm' => 'required',
    ];

    public function mount()
    {
        Log::info('ServiceBooking component mounted');
    }

    public function test()
    {
        Log::info('Test button clicked');
        $this->name = 'Test Name';
    }

    public function submit()
    {
        Log::info('Submit called', [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'typeOfService' => $this->typeOfService,
            'vrm' => $this->vrm,
        ]);

        $this->validate();

        session()->flash('message', 'Booking submitted successfully');

        return redirect()->route('repairs.index');
    }

    public function render()
    {
        Log::info('Rendering ServiceBooking component');

        return view('livewire.service-booking');
    }
}
