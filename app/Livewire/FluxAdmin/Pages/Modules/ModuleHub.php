<?php

namespace App\Livewire\FluxAdmin\Pages\Modules;

use App\Models\PcnCase;
use App\Support\FluxAdminAccess;
use App\Support\FluxAdminModuleRegistry;
use App\Support\FluxAdminPageAccess;
use App\Support\PcnDashboardData;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class ModuleHub extends Component
{
    public string $module;

    #[Url(history: true)]
    public string $listSort = 'desc';

    public function mount(string $module): void
    {
        $this->module = $module;

        if ($module === 'rentals') {
            $this->redirect(route('flux-admin.rental-operations.index'), navigate: true);

            return;
        }

        $config = FluxAdminModuleRegistry::get($module);
        abort_if($config === null, 404);
        abort_unless(FluxAdminPageAccess::allows(backpack_user() ?? FluxAdminAccess::user(), 'flux-admin.modules.show', $module), 403);

        if ($module === 'pcn' && ! in_array($this->listSort, ['asc', 'desc'], true)) {
            $this->listSort = 'desc';
        }
    }

    public function sendReminder(int $id): void
    {
        abort_unless($this->module === 'pcn', 404);

        $pcn = PcnCase::findOrFail($id);
        $pcn->is_whatsapp_sent = true;
        $pcn->whatsapp_last_reminder_sent_at = now();
        $pcn->save();

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Reminder recorded.');
    }

    public function getTitle(): string
    {
        return (FluxAdminModuleRegistry::get($this->module)['title'] ?? 'Module').' — Flux Admin';
    }

    public function render()
    {
        $config = FluxAdminModuleRegistry::get($this->module) ?? [];
        $config['links'] = FluxAdminPageAccess::visibleLinks($config['links'] ?? []);

        if ($this->module === 'pcn') {
            return view('flux-admin.pages.modules.pcn-hub', array_merge(
                ['config' => $config],
                PcnDashboardData::build($this->listSort)
            ));
        }

        return view('flux-admin.pages.modules.hub', ['config' => $config]);
    }
}
