<?php

namespace App\Http\Controllers\Admin;

use App\Models\SupportConversation;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;

class SupportInboxController extends Controller
{
    public function index()
    {
        $staffUsers = User::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.agreements.migrated.admin.support_inbox', [
            'title' => 'Conversations Inbox',
            'staffUsers' => $staffUsers,
        ]);
    }

    public function conversations(Request $request): JsonResponse
    {
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
            $query->where('assigned_backpack_user_id', backpack_user()?->id);
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

    public function show(int $conversationId): JsonResponse
    {
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
                        'url' => route('support.attachments.show', $attachment),
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

    public function sendMessage(Request $request, int $conversationId): JsonResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:6000'],
        ]);

        $conversation = SupportConversation::query()->findOrFail($conversationId);

        $message = SupportMessage::query()->create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'staff',
            'sender_user_id' => backpack_user()?->id,
            'body' => $validated['body'],
        ]);

        return response()->json([
            'ok' => true,
            'message_id' => $message->id,
        ]);
    }

    public function updateConversation(Request $request, int $conversationId): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', 'string', 'in:open,waiting_for_staff,waiting_for_customer,resolved,closed'],
            'assigned_backpack_user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $conversation = SupportConversation::query()
            ->with('serviceBooking')
            ->findOrFail($conversationId);

        if (array_key_exists('status', $validated)) {
            $conversation->status = $validated['status'] ?: 'open';
        }

        if (array_key_exists('assigned_backpack_user_id', $validated)) {
            $conversation->assigned_backpack_user_id = $validated['assigned_backpack_user_id'] ?: null;
        }

        $conversation->save();

        if ($conversation->serviceBooking && in_array($conversation->status, ['resolved', 'closed'], true)) {
            $conversation->serviceBooking->forceFill([
                'is_dealt' => true,
                'dealt_by_user_id' => backpack_user()?->id,
                'dealt_at' => Carbon::now(),
            ])->save();
        }

        return response()->json([
            'ok' => true,
        ]);
    }
}
