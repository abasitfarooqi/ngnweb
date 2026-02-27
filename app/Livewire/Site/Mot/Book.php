<?php

namespace App\Livewire\Site\Mot;

use App\Models\Branch;
use Livewire\Component;

class Book extends Component
{
    public $branches;
    public $selectedBranch = '';
    public $regNo = '';
    public $make = '';
    public $model = '';
    public $name = '';
    public $email = '';
    public $phone = '';
    public $preferredDate = '';
    public $preferredTime = '';
    public $notes = '';

    protected $rules = [
        'selectedBranch' => 'required|exists:branches,id',
        'regNo'          => 'required|string|min:2|max:10',
        'name'           => 'required|string|min:2',
        'email'          => 'required|email',
        'phone'          => 'required|string|min:10',
        'preferredDate'  => 'required|date|after:today',
        'preferredTime'  => 'required',
    ];

    public function mount()
    {
        $this->branches = Branch::orderBy('name')->get();
    }

    public function submitBooking()
    {
        $this->validate();
        session()->flash('success', 'MOT booking request received! We will contact you shortly to confirm.');
        $this->reset(['regNo', 'make', 'model', 'name', 'email', 'phone', 'preferredDate', 'preferredTime', 'notes']);
    }

    public function render()
    {
        return view('livewire.site.mot.book')
            ->layout('components.layouts.public', [
                'title' => 'Book MOT Test | NGN Motors London',
                'description' => 'Book your motorcycle MOT test online at NGN Motors.',
            ]);
    }
}
