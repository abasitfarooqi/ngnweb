<?php

namespace App\Livewire\Portal\Communications;

use App\Models\Communication;
use App\Models\CommunicationRecipient;
use App\Services\Communications\CommunicationEnquiryStarter;
use App\Services\Communications\CommunicationSchema;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public string $uuid;

    public function mount(string $uuid): void
    {
        $this->uuid = $uuid;
        abort_unless(app(CommunicationSchema::class)->ready(), 404);

        $recipient = $this->recipient();
        if ($recipient->seen_at === null || $recipient->read_at === null) {
            $recipient->forceFill([
                'seen_at' => $recipient->seen_at ?? now(),
                'read_at' => $recipient->read_at ?? now(),
            ])->save();
        }
    }

    public function archive(): void
    {
        $this->recipient()->forceFill(['archived_at' => now()])->save();
        $this->redirectRoute('account.notifications', navigate: true);
    }

    public function startEnquiry(): void
    {
        $conversation = app(CommunicationEnquiryStarter::class)
            ->start($this->communication(), Auth::guard('customer')->user());

        $this->redirectRoute('account.support.thread', ['conversationUuid' => $conversation->uuid], navigate: true);
    }

    public function render()
    {
        $communication = $this->communication();

        return view('livewire.portal.communications.show', [
            'communication' => $communication,
            'recipient' => $this->recipient(),
            'enquiryAllowed' => (bool) data_get($communication->policy_snapshot, 'enquiry_allowed', false),
            'enquiryUuid' => data_get($communication->payload_snapshot, 'enquiry_conversation_uuid'),
        ])->layout('components.layouts.portal', [
            'title' => $communication->title.' | Notifications',
        ]);
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
