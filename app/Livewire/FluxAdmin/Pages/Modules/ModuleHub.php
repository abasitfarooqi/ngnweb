<?php

namespace App\Livewire\FluxAdmin\Pages\Modules;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Support\FluxAdminModuleRegistry;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class ModuleHub extends Component
{
    use WithAuthorization;

    public string $module;

    public function mount(string $module): void
    {
        $this->module = $module;
        $config = FluxAdminModuleRegistry::get($module);
        abort_if($config === null, 404);
    }

    public function getTitle(): string
    {
        return (FluxAdminModuleRegistry::get($this->module)['title'] ?? 'Module').' — Flux Admin';
    }

    public function render()
    {
        $config = FluxAdminModuleRegistry::get($this->module);

        return view('flux-admin.pages.modules.hub', ['config' => $config]);
    }
}
