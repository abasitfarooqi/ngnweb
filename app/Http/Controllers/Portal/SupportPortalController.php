<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use App\Models\SupportAttachment;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Support\SupportChatFileRules;

class SupportPortalController extends Controller
{
    public function startGeneral(): RedirectResponse
    {
        $customerAuth = Auth::guard('customer')->user();
        if (! $customerAuth) {
            abort(403);
        }

        $conversation = SupportConversation::query()->firstOrCreate(
            ['customer_auth_id' => $customerAuth->id, 'service_booking_id' => null],
            ['title' => 'General enquiry', 'topic' => 'General enquiry', 'status' => 'open']
        );

        return redirect()->route('account.support.thread', ['conversationUuid' => $conversation->uuid]);
    }

    public function startFromEnquiry(int $serviceBookingId): RedirectResponse
    {
        $customerAuth = Auth::guard('customer')->user();
        if (! $customerAuth) {
            abort(403);
        }

        $booking = ServiceBooking::query()
            ->forPortalCustomer($customerAuth)
            ->findOrFail($serviceBookingId);

        $conversation = SupportConversation::query()->firstOrCreate(
            [
                'service_booking_id' => $booking->id,
            ],
            [
                'customer_auth_id' => $customerAuth->id,
                'title' => $booking->service_type ?: 'Service enquiry',
                'topic' => $booking->subject ?: $booking->service_type ?: 'Service enquiry',
                'status' => 'open',
            ]
        );

        if (! $booking->conversation_id) {
            $booking->forceFill(['conversation_id' => $conversation->id])->save();
        }

        return redirect()->route('account.support.thread', ['conversationUuid' => $conversation->uuid]);
    }

    public function sendMessage(Request $request, string $conversationUuid): RedirectResponse
    {
        $customerAuth = Auth::guard('customer')->user();
        if (! $customerAuth) {
            abort(403);
        }

        $conversation = SupportConversation::query()
            ->where('uuid', $conversationUuid)
            ->where('customer_auth_id', $customerAuth->id)
            ->firstOrFail();

        $validated = $request->validate([
            'body' => ['nullable', 'string', 'max:4000'],
            'reply_to_message_id' => ['nullable', 'integer'],
            'files' => ['nullable', 'array', 'max:'.SupportChatFileRules::MAX_FILES],
            'files.*' => SupportChatFileRules::eachFileRule(),
        ]);

        $body = trim((string) ($validated['body'] ?? ''));
        $uploads = $request->file('files', []);
        if ($uploads !== [] && ! $customerAuth->customer?->portal_upload_access) {
            return redirect()->route('account.support.thread', ['conversationUuid' => $conversation->uuid])
                ->withErrors(['files' => 'NGN has not enabled document uploads for this account.']);
        }
        if ($body === '' && empty($uploads)) {
            return redirect()
                ->route('account.support.thread', ['conversationUuid' => $conversation->uuid])
                ->withErrors(['body' => 'Please type a message or attach a file.']);
        }

        $replyTo = ! empty($validated['reply_to_message_id'])
            ? SupportMessage::query()->where('conversation_id', $conversation->id)->findOrFail((int) $validated['reply_to_message_id'])
            : null;

        $message = SupportMessage::query()->create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'customer',
            'sender_customer_auth_id' => $customerAuth->id,
            'body' => $body !== '' ? $body : null,
            'reply_to_message_id' => $replyTo?->id,
        ]);

        foreach ($uploads as $upload) {
            $originalName = $upload->getClientOriginalName();
            $mime = $upload->getMimeType();
            $path = $upload->store('support-chat/'.$conversation->uuid, 'local');
            $size = (int) Storage::disk('local')->size($path);

            SupportAttachment::query()->create([
                'message_id' => $message->id,
                'disk' => 'local',
                'path' => $path,
                'original_name' => $originalName,
                'mime' => $mime,
                'size' => $size,
                'uploaded_by_customer_auth_id' => $customerAuth->id,
            ]);
        }

        return redirect()->route('account.support.thread', ['conversationUuid' => $conversation->uuid]);
    }

    public function latestMessage(string $conversationUuid): JsonResponse
    {
        $customerAuth = Auth::guard('customer')->user();
        if (! $customerAuth) {
            abort(403);
        }

        $conversation = SupportConversation::query()
            ->where('uuid', $conversationUuid)
            ->where('customer_auth_id', $customerAuth->id)
            ->firstOrFail();

        $latestMessage = SupportMessage::query()
            ->where('conversation_id', $conversation->id)
            ->latest('id')
            ->first(['id', 'sender_type']);

        return response()
            ->json([
                'latest_message_id' => (int) ($latestMessage?->id ?? 0),
                'latest_sender_type' => $latestMessage?->sender_type,
            ])
            ->header('Cache-Control', 'no-store, private, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    public function messagesHtml(string $conversationUuid)
    {
        $customerAuth = Auth::guard('customer')->user();
        if (! $customerAuth) {
            abort(403);
        }

        $conversation = SupportConversation::query()
            ->where('uuid', $conversationUuid)
            ->where('customer_auth_id', $customerAuth->id)
            ->firstOrFail();

        $messages = SupportMessage::query()
            ->where('conversation_id', $conversation->id)
            ->whereNull('deleted_at')
            ->with(['senderCustomerAuth.customer', 'senderUser', 'attachments' => fn ($q) => $q->whereNull('deleted_at')])
            ->orderBy('id')
            ->get();

        return response()
            ->view('portal.support.partials.thread-messages', compact('messages'))
            ->header('Cache-Control', 'no-store, private, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    public function deleteMessage(string $conversationUuid, int $messageId): RedirectResponse
    {
        $customerAuth = Auth::guard('customer')->user();
        $conversation = SupportConversation::query()->where('uuid', $conversationUuid)->where('customer_auth_id', $customerAuth?->id)->firstOrFail();
        $message = $conversation->messages()->where('id', $messageId)->where('sender_type', 'customer')->where('sender_customer_auth_id', $customerAuth->id)->whereNull('deleted_at')->firstOrFail();
        $message->update(['deleted_at' => now(), 'deleted_by_user_id' => null, 'deleted_by_role' => 'customer']);
        $message->attachments()->whereNull('deleted_at')->update(['deleted_at' => now(), 'deleted_by_user_id' => null, 'deleted_by_role' => 'customer']);

        return redirect()->route('account.support.thread', ['conversationUuid' => $conversation->uuid]);
    }
}
