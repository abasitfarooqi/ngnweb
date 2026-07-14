<?php

namespace App\Livewire\FluxAdmin\Pages\Judopay;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Recurring billing — Flux Admin')]
class RecurringIndex extends Component
{
    use WithAuthorization;

    public function mount(): void
    {
        $this->authorizeModule('see-judopay');

        $this->redirect(route('flux-admin.judopay.index'), navigate: false);
    }

    public function render()
    {
        return view('flux-admin.pages.judopay.recurring-index');
    }
}
