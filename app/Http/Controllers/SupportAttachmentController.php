<?php

namespace App\Http\Controllers;

use App\Models\SupportAttachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupportAttachmentController extends Controller
{
    public function show(SupportAttachment $attachment): StreamedResponse
    {
        $attachment->loadMissing('message.conversation');

        $staff = function_exists('backpack_auth') ? backpack_auth()->user() : null;
        $customerAuth = Auth::guard('customer')->user();

        $conversation = $attachment->message?->conversation;
        if (! $conversation) {
            abort(404);
        }

        if (! $staff && (! $customerAuth || (int) $conversation->customer_auth_id !== (int) $customerAuth->id)) {
            abort(403);
        }

        $disk = $attachment->disk ?: 'public';
        $storage = Storage::disk($disk);

        if (! $storage->exists($attachment->path)) {
            abort(404);
        }

        $filename = $attachment->original_name ?: basename($attachment->path);

        return $storage->download($attachment->path, $filename);
    }
}
