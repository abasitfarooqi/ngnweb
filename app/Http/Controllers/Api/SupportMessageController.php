<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupportMessageResource;
use App\Models\SupportAttachment;
use App\Models\SupportMessage;
use App\Support\SupportChatFileRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SupportMessageController extends Controller
{
    public function showAttachment(Request $request, int $attachmentId)
    {
        $customerAuth = $request->user('sanctum') ?: Auth::guard('customer')->user();
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $attachment = SupportAttachment::query()
            ->with('message.conversation')
            ->findOrFail($attachmentId);

        if ((int) $attachment->message->conversation->customer_auth_id !== (int) $customerAuth->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $disk = $attachment->disk ?: 'public';
        if (! Storage::disk($disk)->exists($attachment->path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return Storage::disk($disk)->download($attachment->path, $attachment->original_name);
    }

    public function attachFiles(Request $request, int $messageId)
    {
        $customerAuth = $request->user('sanctum') ?: Auth::guard('customer')->user();
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $message = SupportMessage::query()
            ->with('conversation')
            ->findOrFail($messageId);

        if ((int) $message->conversation->customer_auth_id !== (int) $customerAuth->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'files' => ['required', 'array', 'max:5'],
            'files.*' => SupportChatFileRules::eachFileRule(),
        ]);

        foreach ($data['files'] as $file) {
            $path = $file->store('support-chat/'.$message->conversation->uuid, 'public');
            SupportAttachment::query()->create([
                'message_id' => $message->id,
                'disk' => 'public',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getMimeType(),
                'size' => (int) $file->getSize(),
                'uploaded_by_customer_auth_id' => $customerAuth->id,
            ]);
        }

        return new SupportMessageResource($message->fresh(['conversation', 'senderCustomerAuth.customer', 'senderUser', 'attachments']));
    }
}
