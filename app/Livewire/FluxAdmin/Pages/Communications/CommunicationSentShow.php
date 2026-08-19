<?php

namespace App\Livewire\FluxAdmin\Pages\Communications;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Communication;
use App\Models\CustomerAuth;
use App\Models\SupportConversation;
use App\Services\Communications\CommunicationEnquiryStarter;
use App\Services\Communications\CommunicationReplyRecorder;
use App\Services\Communications\CommunicationSchema;
use App\Support\FluxAdminAccess;
use App\Support\FluxAdminUnreadBadges;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('flux-admin.layouts.app')]
#[Title('Sent communication - Flux Admin')]
class CommunicationSentShow extends Component
{
    use WithAuthorization;
    use WithFileUploads;

    public Communication $communication;

    public string $replyBody = '';

    /** @var array<int, mixed> */
    public array $replyFiles = [];

    public int $realtimeTick = 0;

    public function mount(Communication $communication): void
    {
        if (! FluxAdminAccess::canViewCommunicationsLog()) {
            abort(403, 'You do not have permission to view communications.');
        }

        $this->communication = $communication->load(['deliveries', 'recipients', 'definition', 'attachments']);
        $this->loadReplies();
        FluxAdminUnreadBadges::markNotificationsSeen();
    }

    #[On('staffCommunicationReply')]
    public function refreshFromRealtime(?string $uuid = null): void
    {
        if ($uuid && $uuid !== $this->communication->uuid) {
            return;
        }

        $this->realtimeTick++;
        $this->communication->refresh();
        $this->communication->load(['deliveries', 'recipients', 'definition', 'attachments']);
        $this->loadReplies();
    }

    public function startEnquiry(): void
    {
        abort_unless(FluxAdminAccess::canManageCommunications(), 403);

        $customerAuthId = (int) ($this->communication->customer_auth_id
            ?: $this->communication->recipients()->value('customer_auth_id'));
        abort_unless($customerAuthId > 0, 403, 'This message has no portal account yet.');

        $customer = CustomerAuth::query()->findOrFail($customerAuthId);
        $conversation = app(CommunicationEnquiryStarter::class)->start($this->communication, $customer);

        $this->redirect(route('flux-admin.support-inbox.index', ['c' => $conversation->id]));
    }

    public function sendReply(): void
    {
        abort_unless(FluxAdminAccess::canManageCommunications(), 403);

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

        $staff = Auth::user();
        abort_unless($staff, 403);

        app(CommunicationReplyRecorder::class)->record($this->communication, $staff, $this->replyBody, $this->replyFiles);
        $this->replyBody = '';
        $this->replyFiles = [];
        $this->communication->refresh()->load(['deliveries', 'recipients', 'definition', 'attachments']);
        $this->loadReplies();
    }

    public function render()
    {
        $this->communication->loadMissing(['deliveries', 'recipients', 'definition', 'attachments']);
        $this->loadReplies();

        $enquiry = $this->enquiryConversation();

        $canManage = FluxAdminAccess::canManageCommunications();

        return view('flux-admin.pages.communications.sent-show', [
            'canManageCommunications' => $canManage,
            'replyAllowed' => $canManage
                && (bool) data_get($this->communication->policy_snapshot, 'reply_allowed', false)
                && app(CommunicationReplyRecorder::class)->ready(),
            'enquiry' => $enquiry,
            'enquiryOpen' => $enquiry !== null && ! in_array((string) $enquiry->status, ['resolved', 'closed'], true),
            'canStartEnquiry' => $canManage
                && (int) ($this->communication->customer_auth_id
                    ?: $this->communication->recipients()->value('customer_auth_id')) > 0,
        ]);
    }

    private function enquiryConversation(): ?SupportConversation
    {
        $uuid = data_get($this->communication->payload_snapshot, 'enquiry_conversation_uuid');
        if (! is_string($uuid) || $uuid === '') {
            return null;
        }

        return SupportConversation::query()->where('uuid', $uuid)->first();
    }

    private function loadReplies(): void
    {
        if (app(CommunicationSchema::class)->repliesReady()) {
            $this->communication->loadMissing('replies');

            return;
        }

        $this->communication->setRelation('replies', collect());
    }
}
