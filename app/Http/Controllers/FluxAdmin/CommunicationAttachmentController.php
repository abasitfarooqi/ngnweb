<?php

namespace App\Http\Controllers\FluxAdmin;

use App\Http\Controllers\Controller;
use App\Models\Communication;
use App\Models\CommunicationAttachment;
use App\Services\Communications\CommunicationSchema;
use App\Support\FluxAdminAccess;

class CommunicationAttachmentController extends Controller
{
    public function __invoke(Communication $communication, string $attachment)
    {
        if (! FluxAdminAccess::canViewCommunicationsLog()) {
            abort(403, 'You do not have permission to view communications.');
        }

        abort_unless(app(CommunicationSchema::class)->ready(), 404);

        $file = CommunicationAttachment::query()
            ->where('communication_id', $communication->id)
            ->where('uuid', $attachment)
            ->firstOrFail();

        return $file->downloadResponse();
    }
}
