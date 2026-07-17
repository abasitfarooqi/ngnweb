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

        return view('livewire.portal.support.thread', compact('messages'))
            ->layout('components.layouts.portal', [
                'title' => 'Support Chat | My Account',
            ]);
    }
}
