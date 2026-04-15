<?php

namespace App\Livewire\FluxAdmin\Partials\Finance;

use App\Models\ApplicationItem;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class ApplicationItemsTab extends Component
{
    public int $applicationId;

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function render()
    {
        $items = ApplicationItem::with('motorbike', 'user')
            ->where('application_id', $this->applicationId)
            ->orderBy('id')
            ->get();

        return view('flux-admin.partials.finance.application-items', [
            'items' => $items,
        ]);
    }
}
