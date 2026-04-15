<?php

namespace App\Livewire\FluxAdmin\Partials\Finance;

use App\Models\ContractExtraItem;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class ExtraItemsTab extends Component
{
    public int $applicationId;

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function render()
    {
        $extras = ContractExtraItem::where('application_id', $this->applicationId)
            ->orderBy('id')
            ->get();

        return view('flux-admin.partials.finance.extra-items', [
            'extras' => $extras,
        ]);
    }
}
