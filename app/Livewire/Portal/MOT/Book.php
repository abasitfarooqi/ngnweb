<?php

namespace App\Livewire\Portal\MOT;

use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Book extends Component
{
    public string $branch_id = '';

    public string $date_of_appointment = '';

    public string $time_slot = '';

    public string $motorbike_reg_no = '';

    public string $motorbike_make = '';

    public string $motorbike_model = '';

    public string $notes = '';

    public array $timeSlots = [
        '09:00' => '09:00 AM',
        '09:30' => '09:30 AM',
        '10:00' => '10:00 AM',
        '10:30' => '10:30 AM',
        '11:00' => '11:00 AM',
        '11:30' => '11:30 AM',
        '12:00' => '12:00 PM',
        '13:00' => '01:00 PM',
        '13:30' => '01:30 PM',
        '14:00' => '02:00 PM',
        '14:30' => '02:30 PM',
        '15:00' => '03:00 PM',
        '15:30' => '03:30 PM',
        '16:00' => '04:00 PM',
        '16:30' => '04:30 PM',
    ];

    protected $rules = [
        'branch_id' => 'required|exists:branches,id',
        'motorbike_reg_no' => 'required|string|min:2|max:10',
        'date_of_appointment' => 'required|date|after:today',
        'time_slot' => 'required',
    ];

    public function mount()
    {
        $profile = Auth::guard('customer')->user()->profile;
        if ($profile && $profile->preferred_branch_id) {
            $this->branch_id = (string) $profile->preferred_branch_id;
        }
    }

    public function submit()
    {
        $this->validate();
        session()->flash('success', 'MOT booking submitted. We will confirm your appointment shortly.');
        $this->reset(['motorbike_reg_no', 'motorbike_make', 'motorbike_model', 'date_of_appointment', 'time_slot', 'notes']);
    }

    public function render()
    {
        $branches = Branch::orderBy('name')->get();

        return view('livewire.portal.mot.book', compact('branches'))
            ->layout('components.layouts.portal', ['title' => 'Book MOT | My Account']);
    }
}
