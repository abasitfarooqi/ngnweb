<?php

namespace App\Livewire\FluxAdmin\Pages\Communications;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\CommunicationAudit;
use App\Models\CommunicationDefinition;
use App\Models\CommunicationPolicy;
use App\Services\Communications\CommunicationAuditRecorder;
use App\Services\Communications\CommunicationEmailPreviewRenderer;
use App\Services\Communications\CommunicationSchema;
use App\Support\FluxAdminAccess;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Communication detail - Flux Admin')]
class CommunicationShow extends Component
{
    use WithAuthorization;

    public ?CommunicationDefinition $communicationDefinition = null;

    public bool $schemaReady = true;

    /**
     * @var list<string>
     */
    public array $missingCommunicationTables = [];

    public function mount(CommunicationDefinition $communicationDefinition): void
    {
        $this->assertCanViewCommunications();

        $schema = app(CommunicationSchema::class);
        $this->schemaReady = $schema->ready();
        $this->missingCommunicationTables = $this->schemaReady ? [] : $schema->missingTables();

        $this->communicationDefinition = $communicationDefinition->loadMissing('policy');

        if (! $this->schemaReady) {
            return;
        }
    }

    public function render(CommunicationEmailPreviewRenderer $previewRenderer)
    {
        $audits = $this->communicationDefinition
            ? CommunicationAudit::query()
                ->with('actor')
                ->where('communication_definition_id', $this->communicationDefinition->id)
                ->orderByDesc('id')
                ->limit(25)
                ->get()
            : new Collection;

        $emailPreview = $this->communicationDefinition
            ? $previewRenderer->forDefinition($this->communicationDefinition)
            : [
                'available' => false,
                'subject' => '',
                'source' => '',
                'error' => 'Communication definition not loaded.',
            ];

        return view('flux-admin.pages.communications.show', [
            'audits' => $audits,
            'emailPreview' => $emailPreview,
            'canManageCommunications' => FluxAdminAccess::canAccessCommunications(),
            'canDisableEmail' => FluxAdminAccess::isSuperAdmin(),
        ]);
    }

    public function togglePolicy(string $field, CommunicationAuditRecorder $audit): void
    {
        $this->assertCanManageCommunications();
        $this->assertCommunicationSchemaReady();

        $allowed = [
            'email_enabled',
            'internal_inbox_enabled',
            'web_push_enabled',
            'mobile_push_enabled',
        ];

        abort_unless(in_array($field, $allowed, true), 404);

        $definition = $this->communicationDefinition;
        abort_if($definition === null, 404);

        $policy = $this->ensurePolicy($definition);
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
            metadata: ['source' => 'flux_admin_communications_show'],
        );

        $this->communicationDefinition->refresh()->load('policy');

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Communication policy updated.');
    }

    public function updatePriority(string $priority, CommunicationAuditRecorder $audit): void
    {
        $this->assertCanManageCommunications();
        $this->assertCommunicationSchemaReady();

        validator(
            ['priority' => $priority],
            ['priority' => ['required', Rule::in(['critical', 'important', 'normal', 'informational'])]]
        )->validate();

        $definition = $this->communicationDefinition;
        abort_if($definition === null, 404);

        $policy = $this->ensurePolicy($definition);
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
            metadata: ['source' => 'flux_admin_communications_show'],
        );

        $this->communicationDefinition->refresh()->load('policy');

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Priority updated.');
    }

    public function toggleManaged(CommunicationAuditRecorder $audit): void
    {
        $this->assertCanManageCommunications();

        if (! $this->schemaReady || ! $this->communicationDefinition) {
            abort(503, 'Communication system tables have not been migrated yet.');
        }

        $old = (bool) $this->communicationDefinition->active;
        $new = ! $old;

        $this->communicationDefinition->forceFill(['active' => $new])->save();

        $audit->record(
            event: 'definition_management_changed',
            definition: $this->communicationDefinition,
            field: 'active',
            oldValue: $old,
            newValue: $new,
            metadata: ['source' => 'flux_admin_communications_show'],
        );

        $this->communicationDefinition->refresh()->load('policy');

        $this->dispatch('flux-admin:toast', type: 'success', message: $new
            ? 'Communication is managed by the Transactional Communication System.'
            : 'Communication returned to legacy email behaviour.');
    }

    private function ensurePolicy(CommunicationDefinition $definition): CommunicationPolicy
    {
        if ($definition->policy) {
            return $definition->policy;
        }

        return CommunicationPolicy::query()->create([
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
