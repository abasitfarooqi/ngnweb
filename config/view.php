<?php

return [

    'paths' => [
        resource_path('views'),
        resource_path('views/livewire/agreements/migrated'),
    ],

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

];
