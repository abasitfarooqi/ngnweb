<?php

namespace App\Livewire\Portal\Support;

use App\Models\SupportConversation;
use App\Models\SupportMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Url;

class Thread extends Component
{
    public SupportConversation $conversation;

    public int $customerAuthId;

    #[Url(except: '')]
    public string $search = '';

    public function mount(string $conversationUuid): void
    {
        $customerAuth = Auth::guard('customer')->user();
        if (! $customerAuth) {
            abort(403);
        }

        $this->customerAuthId = (int) $customerAuth->id;

        $this->conversation = SupportConversation::query()
            ->where('uuid', $conversationUuid)
            ->where('customer_auth_id', $customerAuth->id)
            ->with('assignedBackpackUser')
            ->firstOrFail();
    }

    public function render()
    {
        $this->conversation->loadMissing('assignedBackpackUser');

        $messages = SupportMessage::query()
            ->where('conversation_id', $this->conversation->id)
            ->whereNull('deleted_at')
            ->when(trim($this->search) !== '', fn ($q) => $q->where('body', 'like', '%'.trim($this->search).'%'))
            ->with(['senderCustomerAuth.customer', 'senderUser', 'attachments' => fn ($q) => $q->whereNull('deleted_at')])
            ->orderBy('id')
            ->get();

        SupportMessage::query()->where('conversation_id', $this->conversation->id)
            ->where('sender_type', 'staff')->whereNull('read_at_customer')
            ->update(['read_at_customer' => now()]);

        $notificationUuid = null;
        foreach ($messages as $message) {
            $uuid = data_get($message->meta, 'communication_uuid');
            if (is_string($uuid) && $uuid !== '') {
                $notificationUuid = $uuid;
                break;
            }
        }

        return view('livewire.portal.support.thread', [
            'messages' => $messages,
            'notificationUuid' => $notificationUuid,
            'notificationOpen' => str_starts_with((string) $this->conversation->topic, 'Notification:')
                && ! in_array((string) $this->conversation->status, ['resolved', 'closed'], true),
        ])
            ->layout('components.layouts.portal', [
                'title' => 'Support Chat | My Account',
        ]);
    }

    public function deleteMessage(int $messageId): void
    {
        $message = $this->conversation->messages()
            ->where('id', $messageId)
            ->where('sender_type', 'customer')
            ->where('sender_customer_auth_id', $this->customerAuthId)
            ->whereNull('deleted_at')
            ->firstOrFail();
        $message->update(['deleted_at' => now(), 'deleted_by_user_id' => null, 'deleted_by_role' => 'customer']);
        $message->attachments()->whereNull('deleted_at')->update(['deleted_at' => now(), 'deleted_by_user_id' => null, 'deleted_by_role' => 'customer']);
    }
}
