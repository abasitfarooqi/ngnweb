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

class SupportPortalController extends Controller
{
    public function startGeneral(): RedirectResponse
    {
        $customerAuth = Auth::guard('customer')->user();
        if (! $customerAuth) {
            abort(403);
        }

        $conversation = SupportConversation::query()->create([
            'customer_auth_id' => $customerAuth->id,
            'title' => 'General enquiry',
            'topic' => 'General enquiry',
            'status' => 'open',
        ]);

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
            'files' => ['nullable', 'array', 'max:5'],
            'files.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,txt'],
        ]);

        $body = trim((string) ($validated['body'] ?? ''));
        $uploads = $request->file('files', []);
        if ($body === '' && empty($uploads)) {
            return redirect()
                ->route('account.support.thread', ['conversationUuid' => $conversation->uuid])
                ->withErrors(['body' => 'Please type a message or attach a file.']);
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

        $latestMessageId = (int) (SupportMessage::query()
            ->where('conversation_id', $conversation->id)
            ->max('id') ?? 0);

        return response()
            ->json([
                'latest_message_id' => $latestMessageId,
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
            ->with(['senderCustomerAuth.customer', 'senderUser', 'attachments'])
            ->orderBy('id')
            ->get();

        return response()
            ->view('portal.support.partials.thread-messages', compact('messages'))
            ->header('Cache-Control', 'no-store, private, must-revalidate')
            ->header('Pragma', 'no-cache');
    }
}
