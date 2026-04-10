<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupportConversationResource;
use App\Http\Resources\SupportMessageResource;
use App\Models\ServiceBooking;
use App\Models\SupportAttachment;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportConversationController extends Controller
{
    public function index(Request $request)
    {
        $customerAuth = $request->user('sanctum') ?: Auth::guard('customer')->user();
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $conversations = SupportConversation::query()
            ->where('customer_auth_id', $customerAuth->id)
            ->when($request->string('type')->toString() === 'general', fn ($q) => $q->whereNull('service_booking_id'))
            ->when($request->string('type')->toString() === 'enquiry', fn ($q) => $q->whereNotNull('service_booking_id'))
            ->when($request->filled('status'), function ($q) use ($request) {
                $status = (string) $request->string('status');
                if ($status === 'waiting_for_staff') {
                    $q->whereIn('status', ['waiting_for_staff', 'awaiting_staff']);

                    return;
                }
                $q->where('status', $status);
            })
            ->when($request->filled('enquiry_id'), fn ($q) => $q->where('service_booking_id', (int) $request->integer('enquiry_id')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = trim((string) $request->string('search'));
                $q->where(function ($nested) use ($term): void {
                    $nested
                        ->where('title', 'like', '%'.$term.'%')
                        ->orWhere('topic', 'like', '%'.$term.'%')
                        ->orWhere('uuid', 'like', '%'.$term.'%');
                });
            })
            ->with(['assignedBackpackUser', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->paginate(20);

        return SupportConversationResource::collection($conversations);
    }

    public function store(Request $request)
    {
        $customerAuth = $request->user('sanctum') ?: Auth::guard('customer')->user();
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'topic' => ['nullable', 'string', 'max:255'],
            'service_booking_id' => ['nullable', 'integer'],
        ]);

        $booking = null;
        if (! empty($data['service_booking_id'])) {
            $booking = ServiceBooking::query()
                ->forPortalCustomer($customerAuth)
                ->findOrFail((int) $data['service_booking_id']);
        }

        $conversation = SupportConversation::query()->create([
            'customer_auth_id' => $customerAuth->id,
            'service_booking_id' => $booking?->id,
            'title' => $data['title'] ?: ($booking?->service_type ?: 'General enquiry'),
            'topic' => $data['topic'] ?: ($booking?->subject ?: $booking?->service_type ?: 'General enquiry'),
            'status' => 'open',
        ]);

        if ($booking && ! $booking->conversation_id) {
            $booking->forceFill(['conversation_id' => $conversation->id])->save();
        }

        return new SupportConversationResource($conversation->load('assignedBackpackUser'));
    }

    public function messages(Request $request, string $uuid)
    {
        $customerAuth = $request->user('sanctum') ?: Auth::guard('customer')->user();
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $conversation = SupportConversation::query()
            ->where('uuid', $uuid)
            ->where('customer_auth_id', $customerAuth->id)
            ->firstOrFail();

        $messages = SupportMessage::query()
            ->where('conversation_id', $conversation->id)
            ->with(['conversation', 'senderCustomerAuth.customer', 'senderUser', 'attachments'])
            ->orderBy('id')
            ->paginate(50);

        return SupportMessageResource::collection($messages);
    }

    public function show(Request $request, string $uuid)
    {
        $customerAuth = $request->user('sanctum') ?: Auth::guard('customer')->user();
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $conversation = SupportConversation::query()
            ->where('uuid', $uuid)
            ->where('customer_auth_id', $customerAuth->id)
            ->with(['assignedBackpackUser', 'customerAuth.customer', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->firstOrFail();

        return new SupportConversationResource($conversation);
    }

    public function sendMessage(Request $request, string $uuid)
    {
        $customerAuth = $request->user('sanctum') ?: Auth::guard('customer')->user();
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $conversation = SupportConversation::query()
            ->where('uuid', $uuid)
            ->where('customer_auth_id', $customerAuth->id)
            ->firstOrFail();

        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:4000'],
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
            'sender_type' => 'customer',
            'sender_customer_auth_id' => $customerAuth->id,
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
                'uploaded_by_customer_auth_id' => $customerAuth->id,
            ]);
        }

        return new SupportMessageResource($message->load(['conversation', 'senderCustomerAuth.customer', 'senderUser', 'attachments']));
    }

    public function latestMessage(Request $request, string $uuid): JsonResponse
    {
        $customerAuth = $request->user('sanctum') ?: Auth::guard('customer')->user();
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $conversation = SupportConversation::query()
            ->where('uuid', $uuid)
            ->where('customer_auth_id', $customerAuth->id)
            ->firstOrFail();

        $latestMessageId = (int) (SupportMessage::query()
            ->where('conversation_id', $conversation->id)
            ->max('id') ?? 0);

        return response()->json([
            'latest_message_id' => $latestMessageId,
        ]);
    }
}
