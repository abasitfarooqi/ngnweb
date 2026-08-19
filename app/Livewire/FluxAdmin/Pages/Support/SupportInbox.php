<?php

namespace App\Livewire\FluxAdmin\Pages\Support;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\SupportAttachment;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('flux-admin.layouts.app')]
#[Title('Support inbox — Flux Admin')]
class SupportInbox extends Component
{
    use WithAuthorization;
    use WithFileUploads;

    #[Url(as: 'c', except: null)]
    public ?int $selectedConversationId = null;

    #[Url(as: 's', except: 'all')]
    public string $statusFilter = 'all';

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public string $newMessage = '';

    /** @var array<int, mixed> */
    public array $messageFiles = [];

    public int $latestCustomerMessageId = 0;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->latestCustomerMessageId = $this->latestCustomerMessageId();
    }

    #[On('supportInboxRealtimeTick')]
    public function refreshRealtimeState(): void
    {
        $latest = $this->latestCustomerMessageId();
        if ($latest > $this->latestCustomerMessageId && $this->latestCustomerMessageId > 0) {
            $this->dispatch('support:incoming-message');
        }

        $this->latestCustomerMessageId = $latest;

        if ($this->selectedConversationId) {
            SupportMessage::query()
                ->where('conversation_id', $this->selectedConversationId)
                ->where('sender_type', 'customer')
                ->whereNull('read_at_staff')
                ->update(['read_at_staff' => now()]);
        }

        $this->dispatch('staffUnreadBadgesChanged');
    }

    public function selectConversation(int $id): void
    {
        $this->selectedConversationId = $id;
        $conv = SupportConversation::find($id);
        if ($conv) {
            $conv->messages()->where('sender_type', 'customer')->whereNull('read_at_staff')->update(['read_at_staff' => now()]);
        }

        $this->dispatch('staffUnreadBadgesChanged');
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
            'newMessage' => ['nullable', 'string', 'max:5000'],
            'selectedConversationId' => ['required', 'integer', 'exists:support_conversations,id'],
            'messageFiles' => ['nullable', 'array', 'max:5'],
            'messageFiles.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,txt'],
        ]);

        if (trim($this->newMessage) === '' && $this->messageFiles === []) {
            $this->addError('newMessage', 'Please type a reply or attach a file.');

            return;
        }

        $conversation = SupportConversation::query()->findOrFail($this->selectedConversationId);

        $message = SupportMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'staff',
            'sender_user_id' => auth()->id(),
            'body' => trim($this->newMessage) !== '' ? trim($this->newMessage) : null,
            'read_at_staff' => now(),
        ]);

        foreach ($this->messageFiles as $upload) {
            $path = $upload->store('support-chat/'.$conversation->uuid, 'public');

            SupportAttachment::query()->create([
                'message_id' => $message->id,
                'disk' => 'public',
                'path' => $path,
                'original_name' => $upload->getClientOriginalName(),
                'mime' => $upload->getMimeType(),
                'size' => (int) $upload->getSize(),
                'uploaded_by_user_id' => auth()->id(),
            ]);
        }

        $this->newMessage = '';
        $this->messageFiles = [];
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Sent.');
    }

    protected function latestCustomerMessageId(): int
    {
        return (int) (SupportMessage::query()
            ->where('sender_type', 'customer')
            ->max('id') ?? 0);
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
            ? SupportConversation::with(['customerAuth', 'assignedBackpackUser', 'messages.senderUser', 'messages.attachments'])->find($this->selectedConversationId)
            : null;

        $staffUsers = User::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.support.support-inbox', compact('conversations', 'selected', 'staffUsers'));
    }
}
