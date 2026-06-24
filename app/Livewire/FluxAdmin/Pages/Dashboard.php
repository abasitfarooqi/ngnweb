<?php

namespace App\Livewire\FluxAdmin\Pages;

use App\Support\FluxAdminDashboardStats;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Dashboard — Flux Admin')]
class Dashboard extends Component
{
    public function render()
    {
        return view('flux-admin.pages.dashboard', [
            'stats' => FluxAdminDashboardStats::fluxOverview(),
            'legacy' => FluxAdminDashboardStats::legacy(),
        ]);
    }

    public function refreshStats(): void
    {
        FluxAdminDashboardStats::clearCache();
    }
}
