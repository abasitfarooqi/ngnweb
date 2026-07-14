<?php

namespace App\Livewire\FluxAdmin\Pages\Judopay;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Weekly MIT schedule — Flux Admin')]
class WeeklyMitQueueIndex extends Component
{
    use WithAuthorization;

    public function mount(): void
    {
        $this->authorizeModule('see-judopay');

        $this->redirect(route('flux-admin.judopay.weekly-mit-queue'), navigate: false);
    }

    public function render()
    {
        return view('flux-admin.pages.judopay.weekly-mit-queue-index');
    }
}
