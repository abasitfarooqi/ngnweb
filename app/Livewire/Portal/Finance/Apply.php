<?php

namespace App\Livewire\Portal\Finance;

use Livewire\Component;

/**
 * Portal finance apply URL — customer self-serve wizard deactivated.
 * Redirects to finance browse with enquiry panel (staff create contracts in admin).
 * Legacy markup: resources/views/livewire/portal/finance/apply-legacy-inactive.blade.php (never included).
 */
class Apply extends Component
{
    /*
     * Previous stub (inactive — view expected $motorbike, $step, etc. that were never wired):
     *
     * public $motorbikeId;
     * public function mount($motorbikeId) { $this->motorbikeId = $motorbikeId; }
     */

    public function mount(int $motorbikeId): void
    {
        $this->redirect(
            route('account.finance.browse', ['prefill_used' => $motorbikeId])->withFragment('finance-enquiry')
        );
    }

    public function render()
    {
        return view('livewire.portal.finance.apply')
            ->layout('components.layouts.portal', ['title' => 'Finance | My Account']);
    }
}
