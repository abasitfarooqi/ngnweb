<?php

namespace App\Livewire\FluxAdmin\Pages\Tracking;

use App\Models\TrackingSource;
use App\Services\Tracking\TrackingAgreementService;
use App\Support\FluxAdminAccess;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Location tracking — Flux Admin')]
class Index extends Component
{
    public function mount(): void
    {
        if (! FluxAdminAccess::canManagePortalUsers()) {
            throw new AuthorizationException('You do not have permission to access tracking.');
        }
    }

    public function render()
    {
        $sources = TrackingSource::query()->with(['customer', 'vehicle'])->latest('updated_at')->paginate(25);
        return view('flux-admin.pages.tracking.index', ['sources' => $sources, 'enabled' => (bool) config('tracking.enabled')]);
    }
}
