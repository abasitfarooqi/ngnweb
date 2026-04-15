<?php

namespace App\Livewire\FluxAdmin\Pages\Finance;

use App\Models\FinanceApplication;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Finance Application — Flux Admin')]
class FinanceShow extends Component
{
    public FinanceApplication $application;

    public string $activeTab = 'contract';

    public function mount(FinanceApplication $application): void
    {
        $this->application = $application->load('customer', 'user', 'items');
    }

    public function getContractType(): string
    {
        return match (true) {
            (bool) $this->application->is_subscription => 'Subscription',
            (bool) $this->application->is_new_latest => 'New Latest',
            (bool) $this->application->is_used_latest => 'Used Latest',
            (bool) $this->application->is_used_extended_custom => 'Used Extended Custom',
            (bool) $this->application->is_used_extended => 'Used Extended',
            (bool) $this->application->is_used => 'Used',
            default => 'Unknown',
        };
    }

    public function render()
    {
        return view('flux-admin.pages.finance.show');
    }
}
