<?php

namespace App\Livewire\Portal\Support;

use App\Models\SupportConversation;
use App\Models\SupportMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Thread extends Component
{
    public SupportConversation $conversation;

    public int $customerAuthId;

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
            ->with(['senderCustomerAuth.customer', 'senderUser', 'attachments'])
            ->orderBy('id')
            ->get();

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
}
