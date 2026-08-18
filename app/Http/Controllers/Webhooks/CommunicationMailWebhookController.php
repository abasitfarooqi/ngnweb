<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\Communications\CommunicationMailWebhookProcessor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommunicationMailWebhookController extends Controller
{
    public function __invoke(Request $request, CommunicationMailWebhookProcessor $processor): Response
    {
        $expected = (string) config('communications.webhook_token', '');
        if ($expected === '') {
            abort(404);
        }

        $provided = (string) (
            $request->header('X-Communications-Webhook-Token')
            ?: $request->bearerToken()
            ?: $request->query('token')
            ?: $request->input('token')
            ?: ''
        );

        if (! hash_equals($expected, $provided)) {
            abort(403);
        }

        $payload = $request->all();
        if ($payload === [] && is_array($request->json()?->all())) {
            $payload = $request->json()->all();
        }

        $processor->process($payload);

        return response('ok', 200);
    }
}
