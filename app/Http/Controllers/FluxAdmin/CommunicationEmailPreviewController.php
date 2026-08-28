<?php

namespace App\Http\Controllers\FluxAdmin;

use App\Http\Controllers\Controller;
use App\Models\CommunicationDefinition;
use App\Services\Communications\CommunicationEmailPreviewRenderer;
use App\Support\Communications\CommunicationStaffRedactor;
use App\Support\FluxAdminAccess;

class CommunicationEmailPreviewController extends Controller
{
    public function __invoke(
        CommunicationDefinition $communicationDefinition,
        CommunicationEmailPreviewRenderer $previewRenderer,
    ) {
        if (! FluxAdminAccess::canAccessCommunications()) {
            abort(403, 'You do not have permission to access communications.');
        }

        try {
            $preview = $previewRenderer->forDefinition($communicationDefinition);
        } catch (\Throwable $exception) {
            report($exception);
            abort(404, 'Preview unavailable.');
        }

        if (! $preview['available']) {
            abort(404, $preview['error'] ?? 'Preview unavailable.');
        }

        return response(CommunicationStaffRedactor::html($preview['html'] ?? ''), 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'X-Frame-Options' => 'SAMEORIGIN',
        ]);
    }
}
