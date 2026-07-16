<?php

namespace App\Livewire\FluxAdmin\Pages\Pcn;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Legacy URL — dashboard content now lives on /modules/pcn.
 */
#[Layout('flux-admin.layouts.app')]
class PcnDashboard extends Component
{
    use WithAuthorization;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-pcns');
        $this->redirect(route('flux-admin.modules.show', 'pcn'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.pcn.dashboard-redirect');
    }
}
