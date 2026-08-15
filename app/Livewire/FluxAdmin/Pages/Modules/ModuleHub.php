<?php

namespace App\Livewire\FluxAdmin\Pages\Modules;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\PcnCase;
use App\Support\FluxAdminAccess;
use App\Support\FluxAdminModuleRegistry;
use App\Support\PcnDashboardData;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class ModuleHub extends Component
{
    use WithAuthorization;

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

        if ($module === 'pcn') {
            $this->authorizeModule('see-menu-pcns');

            if (! in_array($this->listSort, ['asc', 'desc'], true)) {
                $this->listSort = 'desc';
            }
        }

        if ($module === 'rentals') {
            $this->authorizeModule('see-menu-rentals');
        }

        $permissions = [
            'finance' => ['see-menu-finance'],
            'customers' => ['see-menu-commons'],
            'pcn' => ['see-menu-pcns'],
            'vehicles' => 'see-menu-vehicles',
            'vehicle-records' => 'see-menu-commons',
            'services' => 'see-menu-services-and-repairs-and-report',
            'club' => 'see-menu-commons',
            'delivery' => 'see-menu-commons',
            'claims' => 'see-menu-claims',
            'ecommerce' => 'see-menu-ecommerce',
            'spare-parts' => 'see-menu-inventory',
            'inventory' => 'see-menu-inventory',
            'orders' => 'Admin',
            'blog' => 'see-menu-commons',
            'chat' => 'see-menu-commons',
            'b2b' => 'see-menu-b2b',
            'surveys' => 'see-menu-surveys',
            'misc' => 'Admin',
            'security' => 'see-menu-security',
            'permissions' => 'see-menu-permissions',
            'judopay' => ['see-judopay-home', 'see-judopay'],
        ];

        if (isset($permissions[$module])) {
            $required = $permissions[$module];

            if ($module === 'orders' || $module === 'misc') {
                abort_unless(FluxAdminAccess::isAdmin(backpack_user()), 403);
            } elseif (is_array($required)) {
                $this->authorizeAny($required);
            } else {
                $this->authorizeModule($required);
            }
        }
    }

    /** @param list<string> $permissions */
    protected function authorizeAny(array $permissions): void
    {
        $user = backpack_user();

        abort_unless($user, 403);

        if (FluxAdminAccess::isSuperAdmin($user) || FluxAdminAccess::isAdmin($user)) {
            return;
        }

        foreach ($permissions as $permission) {
            if (method_exists($user, 'can') && $user->can($permission)) {
                return;
            }
        }

        abort(403);
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
        $config = FluxAdminModuleRegistry::get($this->module);

        if ($this->module === 'pcn') {
            return view('flux-admin.pages.modules.pcn-hub', array_merge(
                ['config' => $config],
                PcnDashboardData::build($this->listSort)
            ));
        }

        return view('flux-admin.pages.modules.hub', ['config' => $config]);
    }
}
