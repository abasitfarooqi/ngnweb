<?php

namespace App\Livewire\FluxAdmin\Pages;

use App\Support\FluxAdminGlobalSearch;
use App\Support\FluxAdminSearchRegistry;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Global search — Flux Admin')]
class GlobalSearchIndex extends Component
{
    #[Url(as: 'q', except: '')]
    public string $query = '';

    public function render()
    {
        $payload = $this->query !== ''
            ? FluxAdminGlobalSearch::search($this->query)
            : ['results' => [], 'total' => 0, 'resources_searched' => 0];

        return view('flux-admin.pages.global-search', [
            'results' => $payload['results'],
            'total' => $payload['total'],
            'resourcesSearched' => $payload['resources_searched'],
            'registryCount' => FluxAdminSearchRegistry::count(),
        ]);
    }
}
