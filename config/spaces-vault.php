<?php

return [

    /*
    | Hidden Flux Admin route segment (full URL: /flux-admin/{path}).
    | Not linked in sidebar or global search — bookmark the URL directly.
    */
    'path' => env('SPACES_VAULT_PATH', '_vault/do-spaces'),

    'disk' => env('SPACES_VAULT_DISK', 'spaces'),

    /** Super Admin always allowed. Others only when listed (comma-separated user IDs). */
    'allowed_user_ids' => array_values(array_filter(array_map(
        static fn (string $id): int => (int) trim($id),
        explode(',', (string) env('SPACES_VAULT_USER_IDS', ''))
    ))),

];
