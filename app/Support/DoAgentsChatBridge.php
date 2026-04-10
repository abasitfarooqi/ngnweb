<?php

namespace App\Support;

use App\Models\SupportConversation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DoAgentsChatBridge
{
    public function linkExternalSession(SupportConversation $conversation, ?string $externalSessionId = null): SupportConversation
    {
        $conversation->external_ai_session_id = $externalSessionId ?: $conversation->external_ai_session_id ?: (string) Str::uuid();
        $conversation->save();

        return $conversation;
    }

    /**
     * Fetches transcript from DigitalOcean Agents endpoint when configured.
     * Returns null when integration is disabled or endpoint is unavailable.
     */
    public function fetchExternalHistory(SupportConversation $conversation): ?array
    {
        $cfg = config('support_chat.do_agents');
        if (! ($cfg['enabled'] ?? false)) {
            return null;
        }
        if (! $conversation->external_ai_session_id || empty($cfg['base_url']) || empty($cfg['api_key'])) {
            return null;
        }

        $endpoint = str_replace('{session_id}', urlencode((string) $conversation->external_ai_session_id), (string) $cfg['history_endpoint']);
        $url = rtrim((string) $cfg['base_url'], '/').'/'.ltrim($endpoint, '/');

        $response = Http::withToken((string) $cfg['api_key'])
            ->acceptJson()
            ->timeout(15)
            ->get($url);

        if (! $response->successful()) {
            return null;
        }

        $payload = $response->json();

        return is_array($payload) ? $payload : null;
    }
}
