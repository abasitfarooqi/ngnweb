<?php

return [
    'do_agents' => [
        'enabled' => env('DO_AGENTS_ENABLED', false),
        'base_url' => env('DO_AGENTS_BASE_URL', ''),
        'api_key' => env('DO_AGENTS_API_KEY', ''),
        // If available from DO, this should be the endpoint that returns a conversation/session transcript.
        'history_endpoint' => env('DO_AGENTS_HISTORY_ENDPOINT', '/v1/chat/sessions/{session_id}/messages'),
    ],
];
