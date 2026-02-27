<?php

namespace App\Livewire\Site\Repairs;

use App\Models\Branch;
use Livewire\Component;

class Index extends Component
{
    public $branches;
    public $selectedService = '';
    public $name = '';
    public $email = '';
    public $phone = '';
    public $selectedBranch = '';
    public $regNo = '';
    public $make = '';
    public $model = '';
    public $description = '';

    protected $rules = [
        'name'            => 'required|string|min:2',
        'email'           => 'required|email',
        'phone'           => 'required|string|min:10',
        'selectedBranch'  => 'required|exists:branches,id',
        'selectedService' => 'required|string',
        'description'     => 'required|string|min:10',
    ];

    public function mount()
    {
        $this->branches = Branch::orderBy('name')->get();
    }

    public function submitEnquiry()
    {
        $this->validate();
        session()->flash('success', 'Repair enquiry received! We will contact you shortly.');
        $this->reset(['name', 'email', 'phone', 'regNo', 'make', 'model', 'description', 'selectedService']);
    }

    public function render()
    {
        return view('livewire.site.repairs.index')
            ->layout('components.layouts.public', [
                'title' => 'Motorcycle Repairs & Servicing London | NGN Motors',
                'description' => 'Expert motorcycle repairs, servicing, and maintenance in London.',
            ]);
    }
}
