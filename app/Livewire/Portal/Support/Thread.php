<?php

namespace App\Livewire\Portal\Support;

use App\Models\SupportAttachment;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use App\Support\SupportChatFileRules;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class Thread extends Component
{
    use WithFileUploads;

    public SupportConversation $conversation;

    public int $customerAuthId;

    public string $messageBody = '';

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $messageFiles = [];

    protected function rules(): array
    {
        return array_merge([
            'messageBody' => ['nullable', 'string', 'max:4000'],
        ], SupportChatFileRules::arrayWithFiles('messageFiles', 5));
    }

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
            ->firstOrFail();
    }

    public function sendMessage(): void
    {
        $this->validate();
        if (trim($this->messageBody) === '' && empty($this->messageFiles)) {
            $this->addError('messageBody', 'Please type a message or attach a file.');

            return;
        }

        $customerAuth = Auth::guard('customer')->user();
        if (! $customerAuth) {
            abort(403);
        }

        $message = SupportMessage::query()->create([
            'conversation_id' => $this->conversation->id,
            'sender_type' => 'customer',
            'sender_customer_auth_id' => $customerAuth->id,
            'body' => trim($this->messageBody) !== '' ? trim($this->messageBody) : null,
        ]);

        foreach ($this->messageFiles as $upload) {
            $path = $upload->store('support-chat/'.$this->conversation->uuid, 'public');

            SupportAttachment::query()->create([
                'message_id' => $message->id,
                'disk' => 'public',
                'path' => $path,
                'original_name' => $upload->getClientOriginalName(),
                'mime' => $upload->getMimeType(),
                'size' => (int) $upload->getSize(),
                'uploaded_by_customer_auth_id' => $customerAuth->id,
            ]);
        }

        $this->reset(['messageBody', 'messageFiles']);
        $this->conversation->refresh();

        $this->js('window.__supportThreadSync && window.__supportThreadSync()');
    }

    public function render()
    {
        $this->conversation->refresh();

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
