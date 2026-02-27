<?php

namespace App\Livewire\Portal\MOT;

use Livewire\Component;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;

class Book extends Component
{
    public $branches;
    public $selectedBranch = '';
    public $regNo = '';
    public $make = '';
    public $model = '';
    public $preferredDate = '';
    public $preferredTime = '';
    public $notes = '';

    protected $rules = [
        'selectedBranch' => 'required|exists:branches,id',
        'regNo'          => 'required|string|min:2|max:10',
        'preferredDate'  => 'required|date|after:today',
        'preferredTime'  => 'required',
    ];

    public function mount()
    {
        $this->branches = Branch::orderBy('name')->get();
        $profile = Auth::guard('customer')->user()->profile;
        if ($profile && $profile->preferred_branch_id) {
            $this->selectedBranch = $profile->preferred_branch_id;
        }
    }

    public function submitBooking()
    {
        $this->validate();
        session()->flash('success', 'MOT booking submitted. We will confirm your appointment shortly.');
        $this->reset(['regNo', 'make', 'model', 'preferredDate', 'preferredTime', 'notes']);
    }

    public function render()
    {
        return view('livewire.portal.mot.book')
            ->layout('components.layouts.portal');
    }
}
