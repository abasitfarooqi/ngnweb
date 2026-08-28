<?php

namespace App\Livewire\Portal\Communications;

use App\Models\Communication;
use App\Models\CommunicationRecipient;
use App\Services\Communications\CommunicationEnquiryStarter;
use App\Services\Communications\CommunicationInboxClaimer;
use App\Services\Communications\CommunicationReplyRecorder;
use App\Services\Communications\CommunicationSchema;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class Show extends Component
{
    use WithFileUploads;

    public string $uuid;

    public string $replyBody = '';

    public int $realtimeTick = 0;

    /** @var array<int, mixed> */
    public array $replyFiles = [];

    public function mount(string $uuid): void
    {
        $this->uuid = $uuid;
        abort_unless(app(CommunicationSchema::class)->ready(), 404);

        $customer = Auth::guard('customer')->user();
        abort_unless($customer, 403);
        app(CommunicationInboxClaimer::class)->claimFor($customer);

        $recipient = $this->recipient();
        if ($recipient->seen_at === null || $recipient->read_at === null) {
            $recipient->forceFill([
                'seen_at' => $recipient->seen_at ?? now(),
                'read_at' => $recipient->read_at ?? now(),
            ])->save();
        }
    }

    #[On('customerCommunicationReply')]
    public function refreshFromRealtime(?string $uuid = null): void
    {
        if ($uuid && $uuid !== $this->uuid) {
            return;
        }

        $this->realtimeTick++;
    }

    public function archive(): void
    {
        $this->recipient()->forceFill(['archived_at' => now()])->save();
        $this->redirectRoute('account.notifications', navigate: true);
    }

    public function unarchive(): void
    {
        $this->recipient()->forceFill(['archived_at' => null])->save();
        $this->redirectRoute('account.notifications.show', ['uuid' => $this->uuid], navigate: true);
    }

    public function startEnquiry(): void
    {
        $conversation = app(CommunicationEnquiryStarter::class)
            ->start($this->communication(), Auth::guard('customer')->user());

        $this->redirectRoute('account.support.thread', ['conversationUuid' => $conversation->uuid], navigate: true);
    }

    public function sendReply(): void
    {
        $this->validate(array_merge([
            'replyBody' => ['nullable', 'string', 'max:5000'],
        ], [
            'replyFiles' => ['nullable', 'array', 'max:5'],
            'replyFiles.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,txt'],
        ]));

        if (trim($this->replyBody) === '' && $this->replyFiles === []) {
            $this->addError('replyBody', 'Please type a reply or attach a file.');

            return;
        }

        app(CommunicationReplyRecorder::class)->record(
            $this->communication(),
            Auth::guard('customer')->user(),
            $this->replyBody,
            $this->replyFiles,
        );

        $this->replyBody = '';
        $this->replyFiles = [];
    }

    public function render()
    {
        $communication = $this->communication();
        if (app(CommunicationSchema::class)->repliesReady()) {
            $communication->load(['attachments', 'replies']);
        } else {
            $communication->load(['attachments']);
            $communication->setRelation('replies', collect());
        }

        $enquiry = $this->enquiryConversation($communication);

        return view('livewire.portal.communications.show', [
            'communication' => $communication,
            'recipient' => $this->recipient(),
            'replyAllowed' => app(CommunicationReplyRecorder::class)->ready(),
            'enquiry' => $enquiry,
            'enquiryOpen' => $enquiry !== null && ! in_array((string) $enquiry->status, ['resolved', 'closed'], true),
        ])->layout('components.layouts.portal', [
            'title' => $communication->title.' | Notifications',
        ]);
    }

    private function enquiryConversation(Communication $communication): ?\App\Models\SupportConversation
    {
        $uuid = data_get($communication->payload_snapshot, 'enquiry_conversation_uuid');
        if (! is_string($uuid) || $uuid === '') {
            return null;
        }

        return \App\Models\SupportConversation::query()
            ->where('uuid', $uuid)
            ->where('customer_auth_id', Auth::guard('customer')->id())
            ->first();
    }

    private function communication(): Communication
    {
        $customer = Auth::guard('customer')->user();
        abort_unless($customer, 403);

        return Communication::query()
            ->where('uuid', $this->uuid)
            ->whereHas('recipients', fn ($q) => $q->where('customer_auth_id', $customer->id))
            ->firstOrFail();
    }

    private function recipient(): CommunicationRecipient
    {
        $customer = Auth::guard('customer')->user();
        abort_unless($customer, 403);

        return CommunicationRecipient::query()
            ->where('customer_auth_id', $customer->id)
            ->whereHas('communication', fn ($q) => $q->where('uuid', $this->uuid))
            ->firstOrFail();
    }
}
