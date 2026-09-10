<?php

namespace App\Livewire\FluxAdmin\Pages\Support;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\SupportAttachment;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use App\Models\User;
use App\Support\FluxAdminAccess;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('flux-admin.layouts.app')]
#[Title('Support inbox — Flux Admin')]
class SupportInbox extends Component
{
    use WithAuthorization;
    use WithFileUploads;

    public ?int $selectedConversationId = null;

    #[Url(as: 's', except: 'all')]
    public string $statusFilter = 'all';

    #[Url(as: 'inbox_q', except: '')]
    public string $search = '';

    public string $newMessage = '';
    public ?int $replyToMessageId = null;

    /** @var array<int, mixed> */
    public array $messageFiles = [];

    public int $latestCustomerMessageId = 0;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        if (! FluxAdminAccess::userHasPermission(FluxAdminAccess::user(), 'view-chat') && ! FluxAdminAccess::canManageCommunications()) {
            abort(403);
        }

        $fromQuery = (int) request()->query('c', 0);
        if ($fromQuery > 0) {
            $this->selectedConversationId = $fromQuery;
        }

        $this->latestCustomerMessageId = $this->latestCustomerMessageId();
    }

    #[On('supportInboxRealtimeTick')]
    public function refreshRealtimeState(): void
    {
        $latest = $this->latestCustomerMessageId();
        if ($latest > $this->latestCustomerMessageId && $this->latestCustomerMessageId > 0) {
            $this->dispatch('support:incoming-message');
            $this->dispatch('support-inbox-scroll-bottom');
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
        if ($conv && ! $this->canAccessConversation($conv)) {
            abort(403);
        }
        if ($conv) {
            $conv->messages()->where('sender_type', 'customer')->whereNull('read_at_staff')->update(['read_at_staff' => now()]);
        }

        $this->dispatch('staffUnreadBadgesChanged');
        $this->dispatch('support-inbox-scroll-bottom');
    }

    public function assignToMe(): void
    {
        if (! $this->selectedConversationId) return;
        $conversation = SupportConversation::findOrFail($this->selectedConversationId);
        if (! $this->canAccessConversation($conversation)) abort(403);
        $conversation->update(['assigned_backpack_user_id' => $this->adminUserId()]);
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Assigned to you.');
    }

    public function setStatus(string $status): void
    {
        if (! $this->selectedConversationId) return;
        $conversation = SupportConversation::findOrFail($this->selectedConversationId);
        if (! $this->canAccessConversation($conversation)) abort(403);
        $conversation->update(['status' => $status]);
        $this->dispatch('flux-admin:toast', type: 'success', message: "Status set to {$status}.");
    }

    public function sendMessage(): void
    {
        $this->validate([
            'newMessage' => ['nullable', 'string', 'max:5000'],
            'selectedConversationId' => ['required', 'integer', 'exists:support_conversations,id'],
            'replyToMessageId' => ['nullable', 'integer'],
            'messageFiles' => ['nullable', 'array', 'max:'.\App\Support\SupportChatFileRules::MAX_FILES],
            'messageFiles.*' => \App\Support\SupportChatFileRules::eachFileRule(),
        ]);

        if (trim($this->newMessage) === '' && empty($this->messageFiles)) {
            $this->addError('newMessage', 'Please type a reply or attach a file.');

            return;
        }

        $conversation = SupportConversation::query()->findOrFail($this->selectedConversationId);
        if (! $this->canAccessConversation($conversation)) abort(403);
        $replyTo = $this->replyToMessageId
            ? $conversation->messages()->findOrFail($this->replyToMessageId)
            : null;

        $message = SupportMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'staff',
            'sender_user_id' => $this->adminUserId(),
            'body' => trim($this->newMessage) !== '' ? trim($this->newMessage) : null,
            'reply_to_message_id' => $replyTo?->id,
            'read_at_staff' => now(),
        ]);

        foreach ($this->messageFiles as $upload) {
            // Read metadata while the Livewire temporary file is still available.
            $originalName = $upload->getClientOriginalName();
            $mime = $upload->getMimeType();
            $path = $upload->store('support-chat/'.$conversation->uuid, 'local');
            $size = (int) Storage::disk('local')->size($path);

            SupportAttachment::query()->create([
                'message_id' => $message->id,
                'disk' => 'local',
                'path' => $path,
                'original_name' => $originalName,
                'mime' => $mime,
                'size' => $size,
                'uploaded_by_user_id' => $this->adminUserId(),
            ]);
        }

        $this->newMessage = '';
        $this->messageFiles = [];
        $this->replyToMessageId = null;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Sent.');
        $this->dispatch('support-inbox-scroll-bottom');
    }

    public function deleteMessage(int $messageId): void
    {
        $message = SupportMessage::query()->with('conversation')->findOrFail($messageId);
        if (! $this->canAccessConversation($message->conversation)) {
            abort(403);
        }
        $message->update(['deleted_at' => now(), 'deleted_by_user_id' => $this->adminUserId(), 'deleted_by_role' => 'staff']);
        $message->attachments()->whereNull('deleted_at')->update(['deleted_at' => now(), 'deleted_by_user_id' => $this->adminUserId(), 'deleted_by_role' => 'staff']);
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
            ->when(! \App\Support\FluxAdminAccess::canManageCommunications(), fn ($q) => $q->where('assigned_backpack_user_id', $this->adminUserId()))
            ->orderByDesc('last_message_at')
            ->limit(100)
            ->get();

        $selected = $this->selectedConversationId
            ? SupportConversation::with(['customerAuth', 'assignedBackpackUser', 'messages' => function ($q): void {
                $q->with(['senderUser', 'attachments' => fn ($q) => $q->when(! FluxAdminAccess::canViewDeletedChat(), fn ($q) => $q->whereNull('deleted_at'))])->when(! FluxAdminAccess::canViewDeletedChat(), fn ($q) => $q->whereNull('deleted_at'));
            }])
                ->when(! \App\Support\FluxAdminAccess::canManageCommunications(), fn ($q) => $q->where('assigned_backpack_user_id', $this->adminUserId()))
                ->find($this->selectedConversationId)
            : null;

        $staffUsers = User::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.support.support-inbox', compact('conversations', 'selected', 'staffUsers'));
    }

    private function canAccessConversation(SupportConversation $conversation): bool
    {
        return \App\Support\FluxAdminAccess::canManageCommunications()
            || (int) $conversation->assigned_backpack_user_id === $this->adminUserId();
    }

    private function adminUserId(): int
    {
        return (int) (FluxAdminAccess::user()?->getAuthIdentifier() ?? 0);
    }
}
