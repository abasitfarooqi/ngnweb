<?php

namespace App\Livewire\Site\Contact;

use App\Models\Branch;
use Livewire\Component;

class ServiceBooking extends Component
{
    public $branches;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $branch_id = '';
    public $service_type = '';
    public $regNo = '';
    public $make = '';
    public $model = '';
    public $preferred_date = '';
    public $notes = '';

    protected $rules = [
        'name'          => 'required|string|min:2',
        'email'         => 'required|email',
        'phone'         => 'required|string|min:10',
        'branch_id'     => 'required|exists:branches,id',
        'service_type'  => 'required|string',
        'preferred_date'=> 'required|date|after:today',
    ];

    public function mount()
    {
        $this->branches = Branch::orderBy('name')->get();
    }

    public function submit()
    {
        $this->validate();
        session()->flash('success', 'Service booking request received. We will confirm your appointment shortly.');
        $this->reset(['name', 'email', 'phone', 'branch_id', 'service_type', 'regNo', 'make', 'model', 'preferred_date', 'notes']);
    }

    public function render()
    {
        return view('livewire.site.contact.service-booking')
            ->layout('components.layouts.public', [
                'title' => 'Book a Service Enquiry | NGN Motors',
                'description' => 'Book a motorcycle service at NGN Motors London.',
            ]);
    }
}
