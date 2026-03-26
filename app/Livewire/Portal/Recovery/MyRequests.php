<?php

namespace App\Livewire\Portal\Recovery;

use Livewire\Component;

class MyRequests extends Component
{
    public function render()
    {
        // Recovery requests table not yet linked to customer portal — show empty state
        $requests = collect();

        return view('livewire.portal.recovery.my-requests', compact('requests'))
            ->layout('components.layouts.portal', ['title' => 'My Recovery Requests | My Account']);
    }
}
