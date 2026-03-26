<?php

namespace App\Livewire\Portal;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Club extends Component
{
    public function render()
    {
        $customerAuth = Auth::guard('customer')->user();
        $clubMember = null;

        try {
            $clubMember = DB::table('club_members')
                ->where('email', $customerAuth->email)
                ->first();
        } catch (\Exception $e) {
            // table may not be accessible
        }

        return view('livewire.portal.club', compact('clubMember'))
            ->layout('components.layouts.portal', ['title' => 'NGN Club | My Account']);
    }
}
