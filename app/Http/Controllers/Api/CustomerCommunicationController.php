<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommunicationListResource;
use App\Http\Resources\CommunicationResource;
use App\Models\Communication;
use App\Models\CommunicationAttachment;
use App\Models\CommunicationRecipient;
use App\Models\CustomerAuth;
use App\Services\Communications\CommunicationEnquiryStarter;
use App\Services\Communications\CommunicationInboxClaimer;
use App\Services\Communications\CommunicationReplyRecorder;
use App\Services\Communications\CommunicationSchema;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerCommunicationController extends Controller
{
    public function index(Request $request)
    {
        $customer = $this->customer($request);
        $archived = $request->has('archived') ? $request->boolean('archived') : false;
        $perPage = min(max((int) $request->integer('per_page', 20), 1), 50);

        if (! app(CommunicationSchema::class)->ready()) {
            return CommunicationListResource::collection(new LengthAwarePaginator([], 0, $perPage));
        }

        app(CommunicationInboxClaimer::class)->claimFor($customer);

        $query = Communication::query()
            ->with(['recipients' => fn ($q) => $q->where('customer_auth_id', $customer->id)])
            ->withCount('attachments')
            ->whereHas('recipients', fn ($q) => $q->where('customer_auth_id', $customer->id))
            ->when($request->boolean('unread'), fn ($q) => $q->whereHas('recipients', fn ($r) => $r
                ->where('customer_auth_id', $customer->id)
                ->whereNull('read_at')))
            ->when($archived, fn ($q) => $q->whereHas('recipients', fn ($r) => $r
                ->where('customer_auth_id', $customer->id)
                ->whereNotNull('archived_at')))
            ->when(! $archived, fn ($q) => $q->whereHas('recipients', fn ($r) => $r
                ->where('customer_auth_id', $customer->id)
                ->whereNull('archived_at')))
            ->latest();

        return CommunicationListResource::collection(
            $query->paginate($perPage)
        );
    }

    public function unreadCount(Request $request): array
    {
        $customer = $this->customer($request);

        if (! app(CommunicationSchema::class)->ready()) {
            return ['communications_unread' => 0];
        }

        app(CommunicationInboxClaimer::class)->claimFor($customer);

        return [
            'communications_unread' => CommunicationRecipient::query()
                ->where('customer_auth_id', $customer->id)
                ->whereNull('read_at')
                ->whereNull('archived_at')
                ->count(),
        ];
    }

    public function show(Request $request, string $communication): CommunicationResource
    {
        $this->abortUnlessSchemaReady();

        $relations = [
            'attachments',
            'recipients' => fn ($q) => $q->where('customer_auth_id', $this->customer($request)->id),
        ];
        if (app(CommunicationSchema::class)->repliesReady()) {
            $relations[] = 'replies';
        }

        $record = $this->communicationForCustomer($request, $communication)->load($relations);

        return new CommunicationResource($record);
    }

    public function markRead(Request $request, string $communication): CommunicationResource
    {
        $this->abortUnlessSchemaReady();

        $record = $this->communicationForCustomer($request, $communication);
        $recipient = $this->recipientForCustomer($request, $record);

        $recipient->forceFill([
            'seen_at' => $recipient->seen_at ?? now(),
            'read_at' => now(),
        ])->save();

        return new CommunicationResource($record->fresh(['attachments', 'recipients' => fn ($q) => $q->where('customer_auth_id', $this->customer($request)->id)]));
    }

    public function markUnread(Request $request, string $communication): CommunicationResource
    {
        $this->abortUnlessSchemaReady();

        $record = $this->communicationForCustomer($request, $communication);
        $recipient = $this->recipientForCustomer($request, $record);

        $recipient->forceFill(['read_at' => null])->save();

        return new CommunicationResource($record->fresh(['attachments', 'recipients' => fn ($q) => $q->where('customer_auth_id', $this->customer($request)->id)]));
    }

    public function archive(Request $request, string $communication): CommunicationResource
    {
        $this->abortUnlessSchemaReady();

        $record = $this->communicationForCustomer($request, $communication);
        $recipient = $this->recipientForCustomer($request, $record);

        $recipient->forceFill(['archived_at' => now()])->save();

        return new CommunicationResource($record->fresh(['attachments', 'recipients' => fn ($q) => $q->where('customer_auth_id', $this->customer($request)->id)]));
    }

    public function startEnquiry(Request $request, string $communication)
    {
        $this->abortUnlessSchemaReady();

        $record = $this->communicationForCustomer($request, $communication);
        $conversation = app(CommunicationEnquiryStarter::class)->start($record, $this->customer($request));

        return [
            'enquiry_conversation_uuid' => $conversation->uuid,
            'enquiry_allowed' => true,
        ];
    }

    public function reply(Request $request, string $communication)
    {
        $this->abortUnlessSchemaReady();

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $record = $this->communicationForCustomer($request, $communication);
        $reply = app(CommunicationReplyRecorder::class)->record($record, $this->customer($request), $validated['body']);

        return [
            'id' => $reply->id,
            'author_type' => $reply->author_type,
            'body' => $reply->body,
            'created_at' => optional($reply->created_at)->toIso8601String(),
        ];
    }

    public function showAttachment(Request $request, string $communication, string $attachment)
    {
        $this->abortUnlessSchemaReady();

        $record = $this->communicationForCustomer($request, $communication);
        $file = CommunicationAttachment::query()
            ->where('communication_id', $record->id)
            ->where('uuid', $attachment)
            ->firstOrFail();

        return $file->downloadResponse();
    }

    private function customer(Request $request): CustomerAuth
    {
        $user = $request->user('customer') ?: $request->user();

        abort_unless($user instanceof CustomerAuth, 403);

        return $user;
    }

    private function communicationForCustomer(Request $request, string $uuid): Communication
    {
        $customer = $this->customer($request);

        return Communication::query()
            ->where('uuid', $uuid)
            ->whereHas('recipients', fn ($q) => $q->where('customer_auth_id', $customer->id))
            ->firstOrFail();
    }

    private function abortUnlessSchemaReady(): void
    {
        abort_unless(app(CommunicationSchema::class)->ready(), 503, 'Communication system tables have not been migrated yet.');
    }

    private function recipientForCustomer(Request $request, Communication $communication): CommunicationRecipient
    {
        return $communication->recipients()
            ->where('customer_auth_id', $this->customer($request)->id)
            ->firstOrFail();
    }
}
