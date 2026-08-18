<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Communication;
use App\Models\CommunicationAttachment;
use App\Services\Communications\CommunicationInboxClaimer;
use App\Services\Communications\CommunicationSchema;
use Illuminate\Support\Facades\Auth;

class CommunicationAttachmentController extends Controller
{
    public function __invoke(string $uuid, string $attachment)
    {
        abort_unless(app(CommunicationSchema::class)->ready(), 404);

        $customer = Auth::guard('customer')->user();
        abort_unless($customer, 403);

        app(CommunicationInboxClaimer::class)->claimFor($customer);

        $communication = Communication::query()
            ->where('uuid', $uuid)
            ->whereHas('recipients', fn ($query) => $query->where('customer_auth_id', $customer->id))
            ->firstOrFail();

        $file = CommunicationAttachment::query()
            ->where('communication_id', $communication->id)
            ->where('uuid', $attachment)
            ->firstOrFail();

        return $file->downloadResponse();
    }
}
