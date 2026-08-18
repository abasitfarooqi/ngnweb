<?php

namespace App\Livewire\FluxAdmin\Pages\Communications;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\CommunicationDefinition;
use App\Models\CommunicationPolicy;
use App\Models\SystemSetting;
use App\Services\Communications\CommunicationAuditRecorder;
use App\Services\Communications\CommunicationSchema;
use App\Services\Communications\CommunicationSystemSwitch;
use App\Support\FluxAdminAccess;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Transactional communications - Flux Admin')]
class CommunicationIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithPagination;

    public string $switchReason = '';

    private const LIST_STATE_SESSION_KEY = 'flux_admin.communications.list';

    public function mount(): void
    {
        $this->assertCanViewCommunications();
        $this->restoreListState();
    }

    public function updatedSearch(): void
    {
        $this->rememberListState();
    }

    public function updatedPerPage(): void
    {
        $this->rememberListState();
    }

    public function updatedFilters(): void
    {
        $this->rememberListState();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filters = [];
        $this->perPage = 20;
        session()->forget(self::LIST_STATE_SESSION_KEY);
        $this->resetPage();
    }

    public function enableSystem(CommunicationAuditRecorder $audit): void
    {
        $this->assertCanToggleGlobalSwitch();
        $this->assertCommunicationSchemaReady();
        $this->setGlobalSwitch(true, $audit);
    }

    public function disableSystem(CommunicationAuditRecorder $audit): void
    {
        $this->assertCanToggleGlobalSwitch();
        $this->assertCommunicationSchemaReady();
        $this->setGlobalSwitch(false, $audit);
    }

    public function togglePolicy(int $definitionId, string $field, CommunicationAuditRecorder $audit): void
    {
        $this->assertCanManageCommunications();
        $this->assertCommunicationSchemaReady();

        $allowed = [
            'email_enabled',
            'internal_inbox_enabled',
            'web_push_enabled',
            'mobile_push_enabled',
            'reply_allowed',
            'enquiry_allowed',
        ];

        abort_unless(in_array($field, $allowed, true), 404);

        $definition = CommunicationDefinition::query()->with('policy')->findOrFail($definitionId);
        $policy = $definition->policy ?: CommunicationPolicy::query()->create([
            'communication_definition_id' => $definition->id,
            'email_enabled' => true,
            'internal_inbox_enabled' => false,
            'web_push_enabled' => false,
            'mobile_push_enabled' => false,
            'reply_allowed' => false,
            'enquiry_allowed' => false,
            'mandatory' => false,
            'priority' => $definition->priority,
        ]);

        $old = (bool) $policy->{$field};
        $new = ! $old;

        if ($field === 'email_enabled' && $old === true && $new === false && ! FluxAdminAccess::isSuperAdmin()) {
            abort(403, 'Only Super Admin can disable real transactional email.');
        }

        if ($new === false && $policy->mandatory && in_array($field, ['email_enabled', 'internal_inbox_enabled'], true)) {
            $emailAfter = $field === 'email_enabled' ? false : (bool) $policy->email_enabled;
            $inboxAfter = $field === 'internal_inbox_enabled' ? false : (bool) $policy->internal_inbox_enabled;
            if (! $emailAfter && ! $inboxAfter) {
                $this->dispatch('flux-admin:toast', type: 'error', message: 'Mandatory communications must keep Email or Internal Inbox enabled.');

                return;
            }
        }

        $policy->forceFill([$field => $new])->save();

        $audit->record(
            event: 'policy_changed',
            definition: $definition,
            field: $field,
            oldValue: $old,
            newValue: $new,
            metadata: ['source' => 'flux_admin_communications_index'],
        );

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Communication policy updated.');
    }

    public function toggleManaged(int $definitionId, CommunicationAuditRecorder $audit): void
    {
        $this->assertCanManageCommunications();
        $this->assertCommunicationSchemaReady();

        $definition = CommunicationDefinition::query()->findOrFail($definitionId);
        $old = (bool) $definition->active;
        $new = ! $old;

        $definition->forceFill(['active' => $new])->save();

        $audit->record(
            event: 'definition_management_changed',
            definition: $definition,
            field: 'active',
            oldValue: $old,
            newValue: $new,
            metadata: ['source' => 'flux_admin_communications_index'],
        );

        $this->dispatch('flux-admin:toast', type: 'success', message: $new
            ? 'Communication is managed by the Transactional Communication System.'
            : 'Communication returned to legacy email behaviour.');
    }

    public function updatePriority(int $definitionId, string $priority, CommunicationAuditRecorder $audit): void
    {
        $this->assertCanManageCommunications();
        $this->assertCommunicationSchemaReady();

        validator(
            ['priority' => $priority],
            ['priority' => ['required', Rule::in(['critical', 'important', 'normal', 'informational'])]]
        )->validate();

        $definition = CommunicationDefinition::query()->with('policy')->findOrFail($definitionId);
        $policy = $definition->policy ?: CommunicationPolicy::query()->create([
            'communication_definition_id' => $definition->id,
            'email_enabled' => true,
            'internal_inbox_enabled' => false,
            'web_push_enabled' => false,
            'mobile_push_enabled' => false,
            'reply_allowed' => false,
            'enquiry_allowed' => false,
            'mandatory' => false,
            'priority' => $definition->priority,
        ]);

        $old = (string) $policy->priority;
        if ($old === $priority) {
            return;
        }

        $policy->forceFill(['priority' => $priority])->save();

        $audit->record(
            event: 'policy_changed',
            definition: $definition,
            field: 'priority',
            oldValue: $old,
            newValue: $priority,
            metadata: ['source' => 'flux_admin_communications_index'],
        );

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Priority updated.');
    }

    public function render(CommunicationSystemSwitch $switch, CommunicationSchema $schema)
    {
        $enabled = $switch->enabled();
        $emergencyBypass = (bool) config('communications.emergency_bypass', false);
        $canToggleGlobal = FluxAdminAccess::isSuperAdmin();
        $schemaReady = $schema->ready();

        if ($schemaReady) {
            $definitions = CommunicationDefinition::query()
                ->with('policy')
                ->when($this->search, function ($q, string $term): void {
                    $q->where(function ($nested) use ($term): void {
                        $nested->where('name', 'like', '%'.$term.'%')
                            ->orWhere('key', 'like', '%'.$term.'%')
                            ->orWhere('category', 'like', '%'.$term.'%')
                            ->orWhere('source_class', 'like', '%'.$term.'%')
                            ->orWhere('email_class', 'like', '%'.$term.'%');
                    });
                })
                ->when($this->filter('classification') !== '', fn ($q) => $q->where('classification', $this->filter('classification')))
                ->when($this->filter('category') !== '', fn ($q) => $q->where('category', $this->filter('category')))
                ->when($this->filter('priority') !== '', function ($q): void {
                    $priority = (string) $this->filter('priority');
                    $q->where(function ($inner) use ($priority): void {
                        $inner->whereHas('policy', fn ($p) => $p->where('priority', $priority))
                            ->orWhere(function ($withoutPolicy) use ($priority): void {
                                $withoutPolicy->whereDoesntHave('policy')->where('priority', $priority);
                            });
                    });
                })
                ->when($this->filter('mode') === 'managed', fn ($q) => $q->where('active', true))
                ->when($this->filter('mode') === 'legacy', fn ($q) => $q->where('active', false))
                ->when($this->filter('email') === 'on', fn ($q) => $q->whereHas('policy', fn ($p) => $p->where('email_enabled', true)))
                ->when($this->filter('email') === 'off', fn ($q) => $q->whereHas('policy', fn ($p) => $p->where('email_enabled', false)))
                ->when($this->filter('inbox') === 'on', fn ($q) => $q->whereHas('policy', fn ($p) => $p->where('internal_inbox_enabled', true)))
                ->when($this->filter('inbox') === 'off', fn ($q) => $q->whereHas('policy', fn ($p) => $p->where('internal_inbox_enabled', false)))
                ->when($this->filter('web_push') === 'on', fn ($q) => $q->whereHas('policy', fn ($p) => $p->where('web_push_enabled', true)))
                ->when($this->filter('web_push') === 'off', fn ($q) => $q->whereHas('policy', fn ($p) => $p->where('web_push_enabled', false)))
                ->when($this->filter('mobile_push') === 'on', fn ($q) => $q->whereHas('policy', fn ($p) => $p->where('mobile_push_enabled', true)))
                ->when($this->filter('mobile_push') === 'off', fn ($q) => $q->whereHas('policy', fn ($p) => $p->where('mobile_push_enabled', false)))
                ->when($this->filter('channel') === 'email_on', fn ($q) => $q->whereHas('policy', fn ($p) => $p->where('email_enabled', true)))
                ->when($this->filter('channel') === 'inbox_on', fn ($q) => $q->whereHas('policy', fn ($p) => $p->where('internal_inbox_enabled', true)))
                ->when($this->filter('channel') === 'managed', fn ($q) => $q->where('active', true))
                ->when($this->filter('channel') === 'legacy', fn ($q) => $q->where('active', false))
                ->orderBy('category')
                ->orderBy('name')
                ->paginate($this->perPage);
        } else {
            $definitions = new LengthAwarePaginator([], 0, $this->perPage);
        }

        return view('flux-admin.pages.communications.index', [
            'definitions' => $definitions,
            'enabled' => $enabled,
            'emergencyBypass' => $emergencyBypass,
            'canToggleGlobal' => $canToggleGlobal,
            'schemaReady' => $schemaReady,
            'missingCommunicationTables' => $schemaReady ? [] : $schema->missingTables(),
            'excludedInventory' => (array) config('communications.excluded_inventory', []),
            'filterCategories' => $schemaReady
                ? CommunicationDefinition::query()->whereNotNull('category')->where('category', '!=', '')->distinct()->orderBy('category')->pluck('category')
                : collect(),
            'filterClassifications' => $schemaReady
                ? CommunicationDefinition::query()->whereNotNull('classification')->where('classification', '!=', '')->distinct()->orderBy('classification')->pluck('classification')
                : collect(),
        ]);
    }

    private function rememberListState(): void
    {
        session([
            self::LIST_STATE_SESSION_KEY => [
                'search' => $this->search,
                'filters' => $this->filters,
                'perPage' => $this->perPage,
            ],
        ]);
    }

    private function restoreListState(): void
    {
        $saved = session(self::LIST_STATE_SESSION_KEY);
        if (! is_array($saved)) {
            return;
        }

        if ($this->search === '' && $this->filters === []) {
            $this->search = (string) ($saved['search'] ?? '');
            $this->filters = is_array($saved['filters'] ?? null) ? $saved['filters'] : [];
            $savedPerPage = (int) ($saved['perPage'] ?? 20);
            $this->perPage = in_array($savedPerPage, [20, 50, 100], true) ? $savedPerPage : 20;
        }

        $this->rememberListState();
    }

    private function setGlobalSwitch(bool $enabled, CommunicationAuditRecorder $audit): void
    {
        $key = (string) config('communications.admin_enabled_setting_key', 'communication_system_enabled');
        $setting = SystemSetting::query()->firstOrCreate(
            ['key' => $key],
            ['display_name' => 'Transactional Communication System Enabled', 'value' => '0', 'locked' => true],
        );

        $old = filter_var($setting->value, FILTER_VALIDATE_BOOL);
        $setting->forceFill([
            'display_name' => 'Transactional Communication System Enabled',
            'value' => $enabled ? '1' : '0',
            'locked' => true,
        ])->save();

        $audit->record(
            event: 'global_switch_changed',
            field: $key,
            oldValue: $old,
            newValue: $enabled,
            reason: trim($this->switchReason) !== '' ? trim($this->switchReason) : null,
            metadata: ['source' => 'flux_admin_communications_index'],
        );

        $this->switchReason = '';

        $this->dispatch('flux-admin:toast', type: 'success', message: $enabled
            ? 'Transactional Communication System enabled. Existing Email ON policies still preserve email delivery.'
            : 'Transactional Communication System disabled. Legacy transactional email behavior is active.');
    }

    private function assertCanToggleGlobalSwitch(): void
    {
        if (! FluxAdminAccess::isSuperAdmin()) {
            abort(403, 'Only Super Admin can change the global communication system switch.');
        }
    }

    private function assertCommunicationSchemaReady(): void
    {
        if (! app(CommunicationSchema::class)->ready()) {
            $this->dispatch('flux-admin:toast', type: 'error', message: 'Run the communication system migration before changing policies.');

            abort(503, 'Communication system tables have not been migrated yet.');
        }
    }

    private function assertCanViewCommunications(): void
    {
        if (! FluxAdminAccess::canAccessCommunications()) {
            abort(403, 'This area is restricted to Super Admin.');
        }
    }

    private function assertCanManageCommunications(): void
    {
        if (! FluxAdminAccess::canAccessCommunications()) {
            abort(403, 'This area is restricted to Super Admin.');
        }
    }
}
