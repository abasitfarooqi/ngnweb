<?php

namespace App\Livewire\FluxAdmin\Pages\Communications;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\Communication;
use App\Services\Communications\CommunicationSchema;
use App\Support\FluxAdminAccess;
use App\Support\FluxAdminUnreadBadges;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Sent communications - Flux Admin')]
class CommunicationSentIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithPagination;

    public int $realtimeTick = 0;

    public function mount(): void
    {
        if (! FluxAdminAccess::canViewCommunicationsLog()) {
            abort(403, 'You do not have permission to view communications.');
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

    public function render()
    {
        $schemaReady = app(CommunicationSchema::class)->ready();

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
                ->latest()
                ->paginate($this->perPage)
            : new LengthAwarePaginator([], 0, $this->perPage);

        return view('flux-admin.pages.communications.sent-index', [
            'rows' => $rows,
            'schemaReady' => $schemaReady,
            'canManageCommunications' => FluxAdminAccess::canManageCommunications(),
        ]);
    }
}
