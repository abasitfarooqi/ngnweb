<?php

namespace App\Livewire\Portal\Communications;

use App\Models\Communication;
use App\Models\CommunicationRecipient;
use App\Services\Communications\CommunicationInboxClaimer;
use App\Services\Communications\CommunicationSchema;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public bool $archived = false;

    public int $realtimeTick = 0;

    #[On('customerCommunicationCreated')]
    #[On('customerCommunicationReply')]
    public function refreshFromRealtime(): void
    {
        $this->realtimeTick++;
        $this->resetPage();
    }

    public function showInbox(): void
    {
        $this->archived = false;
        $this->resetPage();
    }

    public function showArchived(): void
    {
        $this->archived = true;
        $this->resetPage();
    }

    public function unarchive(int $id): void
    {
        $customer = Auth::guard('customer')->user();
        abort_unless($customer, 403);

        CommunicationRecipient::query()
            ->where('customer_auth_id', $customer->id)
            ->where('communication_id', $id)
            ->update(['archived_at' => null]);
    }

    public function archive(int $id): void
    {
        $customer = Auth::guard('customer')->user();
        abort_unless($customer, 403);

        CommunicationRecipient::query()
            ->where('customer_auth_id', $customer->id)
            ->where('communication_id', $id)
            ->whereNull('archived_at')
            ->update(['archived_at' => now()]);
    }

    public function render()
    {
        $customer = Auth::guard('customer')->user();
        abort_unless($customer, 403);

        if (app(CommunicationSchema::class)->ready()) {
            app(CommunicationInboxClaimer::class)->claimFor($customer);
        }

        $communications = new LengthAwarePaginator([], 0, 12);

        if (app(CommunicationSchema::class)->ready()) {
            $communications = Communication::query()
                ->with(['recipients' => fn ($q) => $q->where('customer_auth_id', $customer->id)])
                ->withCount('attachments')
                ->whereHas('recipients', function ($query) use ($customer): void {
                    $query->where('customer_auth_id', $customer->id)
                        ->when($this->archived, fn ($q) => $q->whereNotNull('archived_at'))
                        ->when(! $this->archived, fn ($q) => $q->whereNull('archived_at'));
                })
                ->latest()
                ->paginate(12);
        }

        $unread = app(CommunicationSchema::class)->ready()
            ? CommunicationRecipient::query()
                ->where('customer_auth_id', $customer->id)
                ->whereNull('read_at')
                ->whereNull('archived_at')
                ->count()
            : 0;

        return view('livewire.portal.communications.index', [
            'communications' => $communications,
            'unread' => $unread,
        ])->layout('components.layouts.portal', [
            'title' => 'Notifications | My Account',
        ]);
    }
}
