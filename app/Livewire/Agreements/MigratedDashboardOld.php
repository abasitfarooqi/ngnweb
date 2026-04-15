<?php

namespace App\Livewire\Agreements;

use Livewire\Component;

/**
 * Thin Livewire wrapper for the legacy Jetstream dashboard stub (migrated/dashboard-old.blade.php).
 * Register a route when you need this page; it is not routed by default.
 */
class MigratedDashboardOld extends Component
{
    public function render()
    {
        return view('livewire.agreements.migrated.dashboard-old');
    }
}
