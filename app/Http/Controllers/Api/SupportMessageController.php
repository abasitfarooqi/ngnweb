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
        if (! $customerAuth->is_active || ! $customerAuth->customer?->is_active || ! $customerAuth->customer?->is_register) {
            return response()->json(['message' => 'Portal access is inactive.'], 403);
        }

        $attachment = SupportAttachment::query()
            ->with('message.conversation')
            ->findOrFail($attachmentId);

        if ((int) $attachment->message->conversation->customer_auth_id !== (int) $customerAuth->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        if ($attachment->deleted_at) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $disk = $attachment->disk ?: 'local';
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
            'files' => ['required', 'array', 'max:'.SupportChatFileRules::MAX_FILES],
            'files.*' => SupportChatFileRules::eachFileRule(),
        ]);

        foreach ($data['files'] as $file) {
            $originalName = $file->getClientOriginalName();
            $mime = $file->getMimeType();
            $size = (int) $file->getSize();
            $path = $file->store('support-chat/'.$message->conversation->uuid, 'local');
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

        return new SupportMessageResource($message->fresh(['conversation', 'senderCustomerAuth.customer', 'senderUser', 'attachments' => fn ($q) => $q->whereNull('deleted_at')]));
    }
}
