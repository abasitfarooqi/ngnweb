<?php

namespace App\Livewire\FluxAdmin\Pages\Support;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Support inbox — Flux Admin')]
class SupportInbox extends Component
{
    use WithAuthorization;

    #[Url(as: 'c', except: null)]
    public ?int $selectedConversationId = null;

    #[Url(as: 's', except: 'all')]
    public string $statusFilter = 'all';

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public string $newMessage = '';

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    public function selectConversation(int $id): void
    {
        $this->selectedConversationId = $id;
        $conv = SupportConversation::find($id);
        if ($conv) {
            $conv->messages()->where('sender_type', 'customer')->whereNull('read_at_staff')->update(['read_at_staff' => now()]);
        }
    }

    public function assignToMe(): void
    {
        if (! $this->selectedConversationId) return;
        SupportConversation::where('id', $this->selectedConversationId)->update(['assigned_backpack_user_id' => auth()->id()]);
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Assigned to you.');
    }

    public function setStatus(string $status): void
    {
        if (! $this->selectedConversationId) return;
        SupportConversation::where('id', $this->selectedConversationId)->update(['status' => $status]);
        $this->dispatch('flux-admin:toast', type: 'success', message: "Status set to {$status}.");
    }

    public function sendMessage(): void
    {
        $this->validate([
            'newMessage' => ['required', 'string', 'max:5000'],
            'selectedConversationId' => ['required', 'integer', 'exists:support_conversations,id'],
        ]);

        SupportMessage::create([
            'conversation_id' => $this->selectedConversationId,
            'sender_type' => 'staff',
            'sender_user_id' => auth()->id(),
            'body' => $this->newMessage,
            'read_at_staff' => now(),
        ]);

        SupportConversation::where('id', $this->selectedConversationId)->update(['last_message_at' => now()]);
        $this->newMessage = '';
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Sent.');
    }

    public function render()
    {
        $conversations = SupportConversation::query()
            ->with(['customerAuth', 'assignedBackpackUser', 'latestMessage'])
            ->withCount(['messages as unread_customer_count' => fn ($q) => $q->where('sender_type', 'customer')->whereNull('read_at_staff')])
            ->when($this->statusFilter !== 'all' && $this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('title', 'like', "%{$v}%")->orWhere('topic', 'like', "%{$v}%")))
            ->orderByDesc('last_message_at')
            ->limit(100)
            ->get();

        $selected = $this->selectedConversationId
            ? SupportConversation::with(['customerAuth', 'assignedBackpackUser', 'messages.senderUser'])->find($this->selectedConversationId)
            : null;

        $staffUsers = User::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.support.support-inbox', compact('conversations', 'selected', 'staffUsers'));
    }
}
