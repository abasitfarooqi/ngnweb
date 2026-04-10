<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupportConversationResource;
use App\Http\Resources\SupportMessageResource;
use App\Models\SupportAttachment;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class StaffSupportConversationController extends Controller
{
    private function staffUser(Request $request): ?User
    {
        $actor = $request->user('sanctum');

        if (! $actor instanceof User) {
            return null;
        }

        return $this->isBackpackStaff($actor) ? $actor : null;
    }

    public function index(Request $request)
    {
        $staff = $this->staffUser($request);
        if (! $staff) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $perPage = max(1, min(100, (int) $request->integer('per_page', 20)));

        $conversations = SupportConversation::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', (string) $request->string('status')))
            ->when($request->boolean('assigned_to_me'), fn ($q) => $q->where('assigned_backpack_user_id', $staff->id))
            ->when($request->filled('customer_auth_id'), fn ($q) => $q->where('customer_auth_id', (int) $request->integer('customer_auth_id')))
            ->when($request->filled('search'), function ($q) use ($request): void {
                $term = trim((string) $request->string('search'));
                $q->where(function ($nested) use ($term): void {
                    $nested
                        ->where('title', 'like', '%'.$term.'%')
                        ->orWhere('topic', 'like', '%'.$term.'%')
                        ->orWhere('uuid', 'like', '%'.$term.'%');
                });
            })
            ->with([
                'assignedBackpackUser',
                'customerAuth.customer',
                'messages' => fn ($q) => $q->latest()->limit(1),
            ])
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->paginate($perPage);

        return SupportConversationResource::collection($conversations);
    }

    public function inbox(Request $request): JsonResponse
    {
        $staff = $this->staffUser($request);
        if (! $staff) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $query = SupportConversation::query()
            ->with(['customerAuth', 'serviceBooking', 'assignedBackpackUser', 'latestMessage'])
            ->withCount([
                'messages as unread_customer_messages_count' => function ($messageQuery): void {
                    $messageQuery
                        ->where('sender_type', 'customer')
                        ->whereNull('read_at_staff');
                },
            ]);

        $status = (string) $request->string('status', 'all');
        if ($status !== 'all' && $status !== '') {
            if ($status === 'waiting_for_staff') {
                $query->whereIn('status', ['waiting_for_staff', 'awaiting_staff']);
            } else {
                $query->where('status', $status);
            }
        }

        $type = (string) $request->string('type', 'all');
        if ($type === 'general') {
            $query->whereNull('service_booking_id');
        } elseif ($type === 'enquiry') {
            $query->whereNotNull('service_booking_id');
        }

        $assignment = (string) $request->string('assignment', 'all');
        if ($assignment === 'mine') {
            $query->where('assigned_backpack_user_id', $staff->id);
        } elseif ($assignment === 'unassigned') {
            $query->whereNull('assigned_backpack_user_id');
        } elseif ($assignment !== 'all' && ctype_digit($assignment)) {
            $query->where('assigned_backpack_user_id', (int) $assignment);
        }

        $customerId = $request->integer('customer_id');
        if ($customerId > 0) {
            $query->where('customer_auth_id', $customerId);
        }

        $enquiryId = $request->integer('enquiry_id');
        if ($enquiryId > 0) {
            $query->where('service_booking_id', $enquiryId);
        }

        $search = trim((string) $request->string('search', ''));
        if ($search !== '') {
            $query->where(function ($conversationQuery) use ($search): void {
                $conversationQuery
                    ->where('uuid', 'like', '%'.$search.'%')
                    ->orWhere('title', 'like', '%'.$search.'%')
                    ->orWhere('topic', 'like', '%'.$search.'%')
                    ->orWhereHas('customerAuth', function ($customerQuery) use ($search): void {
                        $customerQuery
                            ->where('full_name', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%')
                            ->orWhere('phone', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('serviceBooking', function ($bookingQuery) use ($search): void {
                        $bookingQuery
                            ->where('id', $search)
                            ->orWhere('service_type', 'like', '%'.$search.'%')
                            ->orWhere('subject', 'like', '%'.$search.'%');
                    });
            });
        }

        $items = $query
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->limit(120)
            ->get()
            ->map(function (SupportConversation $conversation): array {
                $latestMessage = $conversation->latestMessage;

                return [
                    'id' => $conversation->id,
                    'uuid' => $conversation->uuid,
                    'title' => $conversation->title ?: 'Conversation #'.$conversation->id,
                    'topic' => $conversation->topic,
                    'status' => $conversation->status,
                    'type' => $conversation->conversation_type,
                    'customer' => [
                        'id' => $conversation->customerAuth?->id,
                        'name' => $conversation->customerAuth?->full_name ?: 'Unknown customer',
                        'email' => $conversation->customerAuth?->email,
                        'phone' => $conversation->customerAuth?->phone,
                    ],
                    'enquiry' => $conversation->service_booking_id ? [
                        'id' => $conversation->service_booking_id,
                        'subject' => $conversation->serviceBooking?->subject ?: $conversation->serviceBooking?->service_type,
                    ] : null,
                    'assignee' => $conversation->assignedBackpackUser ? [
                        'id' => $conversation->assignedBackpackUser->id,
                        'name' => $conversation->assignedBackpackUser->name,
                    ] : null,
                    'unread_count' => (int) $conversation->unread_customer_messages_count,
                    'last_message_at' => $conversation->last_message_at?->toDateTimeString(),
                    'last_message_human' => $conversation->last_message_at?->diffForHumans(),
                    'last_message_preview' => $latestMessage
                        ? str((string) $latestMessage->body)->limit(100)->toString()
                        : null,
                ];
            });

        return response()->json([
            'items' => $items,
        ]);
    }

    public function inboxThread(Request $request, int $conversationId): JsonResponse
    {
        $staff = $this->staffUser($request);
        if (! $staff) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $conversation = SupportConversation::query()
            ->with(['customerAuth', 'serviceBooking', 'assignedBackpackUser'])
            ->findOrFail($conversationId);

        SupportMessage::query()
            ->where('conversation_id', $conversation->id)
            ->where('sender_type', 'customer')
            ->whereNull('read_at_staff')
            ->update(['read_at_staff' => Carbon::now()]);

        $messages = SupportMessage::query()
            ->with('attachments')
            ->where('conversation_id', $conversation->id)
            ->orderBy('id')
            ->limit(200)
            ->get()
            ->map(function (SupportMessage $message): array {
                return [
                    'id' => $message->id,
                    'sender_type' => $message->sender_type,
                    'body' => $message->body,
                    'created_at' => $message->created_at?->toDateTimeString(),
                    'created_human' => $message->created_at?->diffForHumans(),
                    'attachments' => $message->attachments->map(fn ($attachment) => [
                        'id' => $attachment->id,
                        'name' => $attachment->original_name,
                        'url' => route('api.staff.support.attachments.show', $attachment->id),
                    ])->values()->all(),
                ];
            });

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'uuid' => $conversation->uuid,
                'title' => $conversation->title ?: 'Conversation #'.$conversation->id,
                'topic' => $conversation->topic,
                'status' => $conversation->status,
                'type' => $conversation->conversation_type,
                'customer' => [
                    'id' => $conversation->customerAuth?->id,
                    'name' => $conversation->customerAuth?->full_name ?: 'Unknown customer',
                    'email' => $conversation->customerAuth?->email,
                    'phone' => $conversation->customerAuth?->phone,
                ],
                'enquiry' => $conversation->service_booking_id ? [
                    'id' => $conversation->service_booking_id,
                    'subject' => $conversation->serviceBooking?->subject ?: $conversation->serviceBooking?->service_type,
                ] : null,
                'assignee_id' => $conversation->assigned_backpack_user_id,
                'last_message_at' => $conversation->last_message_at?->toDateTimeString(),
            ],
            'messages' => $messages,
        ]);
    }

    public function inboxSendMessage(Request $request, int $conversationId)
    {
        return $this->sendMessage($request, $conversationId);
    }

    public function inboxUpdateMeta(Request $request, int $conversationId)
    {
        return $this->updateConversation($request, $conversationId);
    }

    public function assignees(Request $request): JsonResponse
    {
        $staff = $this->staffUser($request);
        if (! $staff) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $users = User::query()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'email'])
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => trim((string) $user->full_name) !== '' ? trim((string) $user->full_name) : $user->email,
                'email' => $user->email,
                'is_admin' => (bool) $user->is_admin,
            ])
            ->values();

        return response()->json([
            'items' => $users,
        ]);
    }

    public function messages(Request $request, int $conversationId)
    {
        $staff = $this->staffUser($request);
        if (! $staff) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $conversation = SupportConversation::query()->findOrFail($conversationId);

        $messages = SupportMessage::query()
            ->where('conversation_id', $conversation->id)
            ->with(['conversation', 'senderCustomerAuth.customer', 'senderUser', 'attachments'])
            ->orderBy('id')
            ->paginate(50);

        return SupportMessageResource::collection($messages);
    }

    public function show(Request $request, int $conversationId)
    {
        $staff = $this->staffUser($request);
        if (! $staff) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $conversation = SupportConversation::query()
            ->with(['assignedBackpackUser', 'customerAuth.customer', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->findOrFail($conversationId);

        return new SupportConversationResource($conversation);
    }

    public function sendMessage(Request $request, int $conversationId)
    {
        $staff = $this->staffUser($request);
        if (! $staff) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $conversation = SupportConversation::query()->findOrFail($conversationId);

        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:6000'],
            'files' => ['nullable', 'array', 'max:5'],
            'files.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,txt'],
        ]);

        $body = trim((string) ($data['body'] ?? ''));
        $uploads = $request->file('files', []);
        if ($body === '' && empty($uploads)) {
            return response()->json([
                'message' => 'Please type a message or attach a file.',
            ], 422);
        }

        $message = SupportMessage::query()->create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'staff',
            'sender_user_id' => $staff->id,
            'body' => $body !== '' ? $body : null,
        ]);

        foreach ($uploads as $upload) {
            $path = $upload->store('support-chat/'.$conversation->uuid, 'public');

            SupportAttachment::query()->create([
                'message_id' => $message->id,
                'disk' => 'public',
                'path' => $path,
                'original_name' => $upload->getClientOriginalName(),
                'mime' => $upload->getMimeType(),
                'size' => (int) $upload->getSize(),
                'uploaded_by_user_id' => $staff->id,
            ]);
        }

        return new SupportMessageResource($message->fresh(['conversation', 'senderCustomerAuth.customer', 'senderUser', 'attachments']));
    }

    public function latestMessage(Request $request, int $conversationId): JsonResponse
    {
        $staff = $this->staffUser($request);
        if (! $staff) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $conversation = SupportConversation::query()->findOrFail($conversationId);
        $latestMessageId = (int) (SupportMessage::query()
            ->where('conversation_id', $conversation->id)
            ->max('id') ?? 0);

        return response()->json([
            'latest_message_id' => $latestMessageId,
        ]);
    }

    public function updateConversation(Request $request, int $conversationId)
    {
        $staff = $this->staffUser($request);
        if (! $staff) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'status' => ['nullable', 'string', 'in:open,waiting_for_staff,waiting_for_customer,resolved,closed'],
            'assigned_backpack_user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $conversation = SupportConversation::query()
            ->with('assignedBackpackUser')
            ->findOrFail($conversationId);

        if (array_key_exists('status', $validated) && $validated['status']) {
            $conversation->status = $validated['status'];
        }

        if (array_key_exists('assigned_backpack_user_id', $validated)) {
            $conversation->assigned_backpack_user_id = $validated['assigned_backpack_user_id'] ?: null;
        }

        $conversation->save();

        return new SupportConversationResource($conversation->fresh(['assignedBackpackUser', 'customerAuth.customer']));
    }

    public function showAttachment(Request $request, int $attachmentId)
    {
        $staff = $this->staffUser($request);
        if (! $staff) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $attachment = SupportAttachment::query()
            ->with('message.conversation')
            ->findOrFail($attachmentId);

        $disk = $attachment->disk ?: 'public';
        if (! Storage::disk($disk)->exists($attachment->path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return Storage::disk($disk)->download($attachment->path, $attachment->original_name);
    }

    private function isBackpackStaff(User $user): bool
    {
        if ((bool) $user->is_admin) {
            return true;
        }

        if (property_exists($user, 'is_client') && (bool) $user->is_client) {
            return false;
        }

        try {
            if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'super-admin', 'manager', 'staff', 'support'])) {
                return true;
            }
        } catch (\Throwable) {
            // Fall through to permissive check below.
        }

        return true;
    }
}
