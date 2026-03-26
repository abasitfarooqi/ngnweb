<?php

namespace App\Livewire\Portal\Recovery;

use App\Models\Branch;
use Livewire\Component;

class Request extends Component
{
    public string $urgency = 'planned';

    public string $pickup_postcode = '';

    public string $pickup_location = '';

    public string $bike_reg_no = '';

    public string $bike_make = '';

    public string $bike_model = '';

    public string $issue_type = '';

    public string $issue_description = '';

    public string $destination_type = '';

    public string $branch_id = '';

    public string $destination_address = '';

    protected function rules(): array
    {
        $rules = [
            'urgency' => 'required|in:urgent,planned',
            'pickup_postcode' => 'required|string|min:5',
            'pickup_location' => 'required|string|min:5',
            'issue_type' => 'required|string',
            'destination_type' => 'required|in:branch,home,other',
        ];
        if ($this->destination_type === 'branch') {
            $rules['branch_id'] = 'required|exists:branches,id';
        }
        if (in_array($this->destination_type, ['home', 'other'])) {
            $rules['destination_address'] = 'required|string|min:5';
        }

        return $rules;
    }

    public function submit()
    {
        $this->validate();
        session()->flash('success', 'Recovery request submitted. We will contact you shortly.');
        $this->reset([
            'pickup_postcode', 'pickup_location', 'bike_reg_no', 'bike_make', 'bike_model',
            'issue_type', 'issue_description', 'destination_type', 'branch_id', 'destination_address',
        ]);
    }

    public function render()
    {
        $branches = Branch::orderBy('name')->get();

        return view('livewire.portal.recovery.request', compact('branches'))
            ->layout('components.layouts.portal', ['title' => 'Request Recovery | My Account']);
    }
}
