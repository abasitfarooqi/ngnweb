<?php

namespace App\Http\Controllers;

use App\Models\SupportAttachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SupportAttachmentController extends Controller
{
    public function show(SupportAttachment $attachment)
    {
        $staff = backpack_auth()->user();
        $customerAuth = Auth::guard('customer')->user();

        $conversation = $attachment->message?->conversation;
        if (! $conversation) {
            abort(404);
        }

        if (! $staff && (! $customerAuth || (int) $conversation->customer_auth_id !== (int) $customerAuth->id)) {
            abort(403);
        }

        $disk = $attachment->disk ?: 'public';
        if (! Storage::disk($disk)->exists($attachment->path)) {
            abort(404);
        }

        return Storage::disk($disk)->download($attachment->path, $attachment->original_name);
    }
}
