<?php

namespace App\Livewire\Portal\Repairs;

use Livewire\Component;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;

class Request extends Component
{
    public $branches;
    public $selectedBranch = '';
    public $serviceType = '';
    public $regNo = '';
    public $make = '';
    public $model = '';
    public $description = '';
    public $preferredDate = '';

    protected $rules = [
        'selectedBranch' => 'required|exists:branches,id',
        'serviceType'    => 'required|string',
        'description'    => 'required|string|min:10',
        'preferredDate'  => 'required|date|after:today',
    ];

    public function mount()
    {
        $this->branches = Branch::orderBy('name')->get();
        $profile = Auth::guard('customer')->user()->profile;
        if ($profile && $profile->preferred_branch_id) {
            $this->selectedBranch = $profile->preferred_branch_id;
        }
    }

    public function submit()
    {
        $this->validate();
        session()->flash('success', 'Repair request submitted. We will confirm your appointment.');
        $this->reset(['serviceType', 'regNo', 'make', 'model', 'description', 'preferredDate']);
    }

    public function render()
    {
        return view('livewire.portal.repairs.request')
            ->layout('components.layouts.portal');
    }
}
