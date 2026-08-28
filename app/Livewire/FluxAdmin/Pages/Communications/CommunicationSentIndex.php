<?php

namespace App\Livewire\FluxAdmin\Pages\Communications;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\Communication;
use App\Services\Communications\CommunicationSchema;
use App\Support\FluxAdminAccess;
use App\Support\FluxAdminUnreadBadges;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Notifications - Flux Admin')]
class CommunicationSentIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithPagination;

    public int $realtimeTick = 0;

    public function mount(): void
    {
        if (! FluxAdminAccess::canViewCommunicationsLog()) {
            abort(403, 'You do not have permission to view notifications.');
        }

        FluxAdminUnreadBadges::markNotificationsSeen();
    }

    #[On('staffCommunicationCreated')]
    #[On('staffCommunicationReply')]
    public function refreshFromRealtime(): void
    {
        FluxAdminUnreadBadges::markNotificationsSeen();
        $this->realtimeTick++;
        $this->resetPage();
    }

    public function hideFromStaff(int $id): void
    {
        abort_unless(FluxAdminAccess::canViewCommunicationsLog(), 403);
        abort_unless(Schema::hasColumn('communications', 'staff_hidden_at'), 503, 'Run the notification hide migration first.');

        $row = Communication::query()->findOrFail($id);
        $row->forceFill([
            'staff_hidden_at' => now(),
            'staff_hidden_by' => FluxAdminAccess::user()?->getAuthIdentifier(),
        ])->save();

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Hidden from staff. The log is kept.');
    }

    public function unhideFromStaff(int $id): void
    {
        abort_unless(FluxAdminAccess::canViewCommunicationsLog(), 403);
        abort_unless(Schema::hasColumn('communications', 'staff_hidden_at'), 503, 'Run the notification hide migration first.');

        $row = Communication::query()->findOrFail($id);
        $row->forceFill([
            'staff_hidden_at' => null,
            'staff_hidden_by' => null,
        ])->save();

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Shown to staff again.');
    }

    public function render()
    {
        $schemaReady = app(CommunicationSchema::class)->ready();
        $hideReady = $schemaReady && Schema::hasColumn('communications', 'staff_hidden_at');

        $rows = $schemaReady
            ? Communication::query()
                ->with(['deliveries', 'recipients'])
                ->when($this->search !== '', function ($query): void {
                    $term = '%'.$this->search.'%';
                    $query->where(function ($inner) use ($term): void {
                        $inner->where('title', 'like', $term)
                            ->orWhere('recipient_email', 'like', $term)
                            ->orWhere('communication_key', 'like', $term)
                            ->orWhere('subject', 'like', $term);
                    });
                })
                ->when($this->filter('category') !== '', fn ($q) => $q->where('category', $this->filter('category')))
                ->when($this->filter('email') !== '', function ($query): void {
                    $status = (string) $this->filter('email');
                    if ($status === 'none') {
                        $query->whereDoesntHave('deliveries', fn ($d) => $d->where('channel', 'email'));

                        return;
                    }
                    $query->whereHas('deliveries', fn ($d) => $d->where('channel', 'email')->where('status', $status));
                })
                ->when($this->filter('inbox') !== '', function ($query): void {
                    $status = (string) $this->filter('inbox');
                    if ($status === 'off') {
                        $query->whereDoesntHave('deliveries', fn ($d) => $d->where('channel', 'internal_inbox'));

                        return;
                    }
                    $query->whereHas('deliveries', fn ($d) => $d->where('channel', 'internal_inbox')->where('status', $status));
                })
                ->when($hideReady && $this->filter('hidden') !== 'all' && $this->filter('hidden') !== 'hidden', fn ($q) => $q->whereNull('staff_hidden_at'))
                ->when($hideReady && $this->filter('hidden') === 'hidden', fn ($q) => $q->whereNotNull('staff_hidden_at'))
                ->latest()
                ->paginate($this->perPage)
            : new LengthAwarePaginator([], 0, $this->perPage);

        return view('flux-admin.pages.communications.sent-index', [
            'rows' => $rows,
            'schemaReady' => $schemaReady,
            'hideReady' => $hideReady,
            'canManageCommunications' => FluxAdminAccess::canAccessCommunications(),
            'canViewNotifications' => FluxAdminAccess::canViewCommunicationsLog(),
            'filterCategories' => $schemaReady
                ? Communication::query()->whereNotNull('category')->where('category', '!=', '')->distinct()->orderBy('category')->pluck('category')
                : collect(),
        ]);
    }
}
