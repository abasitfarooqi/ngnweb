<?php

namespace App\Livewire\FluxAdmin\Pages\Tracking;

use App\Models\TrackingSource;
use App\Support\FluxAdminAccess;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Tracking history — Flux Admin')]
class Show extends Component
{
    public function mount(TrackingSource $source): void
    {
        if (! FluxAdminAccess::canManagePortalUsers()) {
            throw new AuthorizationException('You do not have permission to access tracking.');
        }
    }

    public function render(TrackingSource $source)
    {
        $source->load(['customer', 'vehicle']);
        $locations = $source->locations()->latest('recorded_at')->limit(200)->get();
        return view('flux-admin.pages.tracking.show', compact('source', 'locations'));
    }
}
