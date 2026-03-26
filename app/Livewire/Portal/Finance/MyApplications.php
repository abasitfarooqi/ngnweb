<?php

namespace App\Livewire\Portal\Finance;

use App\Models\FinanceApplication;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyApplications extends Component
{
    public function render()
    {
        $customerAuth = Auth::guard('customer')->user();
        $profile = $customerAuth->profile;
        $applications = collect();

        if ($profile) {
            try {
                $applications = FinanceApplication::where('customer_id', $profile->id)
                    ->with(['items.motorbike'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            } catch (\Exception $e) {
                $applications = collect();
            }
        }

        return view('livewire.portal.finance.my-applications', compact('applications'))
            ->layout('components.layouts.portal', ['title' => 'Finance Applications | My Account']);
    }
}
