<?php

namespace App\Livewire\Portal\Repairs;

use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Request extends Component
{
    public string $service_type = '';

    public string $bike_reg_no = '';

    public string $bike_make = '';

    public string $bike_model = '';

    public string $mileage = '';

    public string $issue_description = '';

    public bool $needs_collection = false;

    public string $collection_postcode = '';

    public string $collection_address = '';

    public string $date_requested = '';

    public string $time_slot = '';

    public string $branch_id = '';

    public string $repair_authorisation_limit = '0';

    public array $timeSlots = [
        '09:00' => '09:00 AM', '09:30' => '09:30 AM', '10:00' => '10:00 AM',
        '10:30' => '10:30 AM', '11:00' => '11:00 AM', '11:30' => '11:30 AM',
        '12:00' => '12:00 PM', '13:00' => '01:00 PM', '13:30' => '01:30 PM',
        '14:00' => '02:00 PM', '14:30' => '02:30 PM', '15:00' => '03:00 PM',
        '15:30' => '03:30 PM', '16:00' => '04:00 PM', '16:30' => '04:30 PM',
    ];

    protected function rules(): array
    {
        $rules = [
            'service_type' => 'required|string',
            'bike_reg_no' => 'required|string|min:2|max:12',
            'issue_description' => 'nullable|string',
            'date_requested' => 'required|date|after:today',
            'time_slot' => 'required',
            'branch_id' => 'required|exists:branches,id',
        ];
        if ($this->needs_collection) {
            $rules['collection_postcode'] = 'required|string|min:5';
            $rules['collection_address'] = 'required|string|min:5';
        }

        return $rules;
    }

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
        session()->flash('success', 'Repair booking submitted. We will confirm your appointment shortly.');
        $this->reset([
            'service_type', 'bike_reg_no', 'bike_make', 'bike_model', 'mileage',
            'issue_description', 'needs_collection', 'collection_postcode', 'collection_address',
            'date_requested', 'time_slot',
        ]);
    }

    public function render()
    {
        $branches = Branch::orderBy('name')->get();

        return view('livewire.portal.repairs.request', compact('branches'))
            ->layout('components.layouts.portal', ['title' => 'Book Repairs | My Account']);
    }
}
