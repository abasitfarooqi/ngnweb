<?php

namespace App\Livewire\FluxAdmin\Pages\Communications;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Communication;
use App\Support\FluxAdminAccess;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Sent communication - Flux Admin')]
class CommunicationSentShow extends Component
{
    use WithAuthorization;

    public Communication $communication;

    public function mount(Communication $communication): void
    {
        if (! FluxAdminAccess::canAccessCommunications()) {
            abort(403, 'This area is restricted to Super Admin.');
        }

        $this->communication = $communication->load(['deliveries', 'recipients', 'definition']);
    }

    public function render()
    {
        return view('flux-admin.pages.communications.sent-show');
    }
}
