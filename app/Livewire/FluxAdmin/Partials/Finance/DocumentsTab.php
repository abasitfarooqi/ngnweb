<?php

namespace App\Livewire\FluxAdmin\Partials\Finance;

use App\Models\CustomerContract;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class DocumentsTab extends Component
{
    public int $applicationId;

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function render()
    {
        $documents = CustomerContract::where('application_id', $this->applicationId)
            ->orderByDesc('id')
            ->get();

        return view('flux-admin.partials.finance.documents', [
            'documents' => $documents,
        ]);
    }
}
