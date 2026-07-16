<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Legacy URL — redirects to merged /flux-admin/rentals.
 */
#[Layout('flux-admin.layouts.app')]
class BookingsManagementIndex extends Component
{
    public function mount(): void
    {
        $this->redirect(route('flux-admin.rentals.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.partials.loading-placeholder');
    }
}
