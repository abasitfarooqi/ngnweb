<?php

return [

    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],
        'uploads' => [
            'driver' => 'local',
            'root' => storage_path('app/uploads'),
            'url' => env('APP_URL').'/uploads',
            'visibility' => 'public',
        ],

        'avatars' => [
            'driver' => 'local',
            'root' => storage_path('avatars'),
        ],

        'signatures' => [
            'driver' => 'local',
            'root' => storage_path('signatures'),
        ],

        'documents' => [
            'driver' => 'local',
            'root' => storage_path('documents'),
        ],

        'private' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'visibility' => 'private',
        ],

        'ftp' => [
            'driver' => 'ftp',
            'host' => env('FTP_HOST'),
            'username' => env('FTP_USERNAME'),
            'password' => env('FTP_PASSWORD'),

            // Optional FTP Settings...
            // 'port' => env('FTP_PORT', 21),
            // 'root' => env('FTP_ROOT'),
            // 'passive' => true,
            // 'ssl' => true,
            // 'timeout' => 30,
        ],

        'ftp1' => [
            'driver' => 'sftp',
            'host' => env('FTP_HOST_1'),
            'username' => env('FTP_USERNAME_1'),
            'password' => env('FTP_PASSWORD_1'),
            'port' => 22,
            'root' => '/files',
            'timeout' => 30,
        ],

        'datasync' => [
            'driver' => 'sftp',
            'host' => env('DATASYNC_FTP_HOST'),
            'username' => env('DATASYNC_FTP_USERNAME'),
            'password' => env('DATASYNC_FTP_PASSWORD'),
            'port' => env('DATASYNC_FTP_PORT', 22),
            'timeout' => 30,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        'used_motorbikes' => [
            'driver' => 'local',
            'root' => storage_path('app/public/motorbikes'),
            'url' => env('APP_URL').'/storage/motorbikes',
            'visibility' => 'public',
            'throw' => false,
        ],

        'product_images' => [
            'driver' => 'local',
            'root' => public_path('assets/images/store/products'),
            'url' => env('APP_URL').'/assets/images/store/products',
            'visibility' => 'public',
        ],

        'new_motorbike_images' => [
            'driver' => 'local',
            'root' => storage_path('app/public/motorbikes'),
            'url' => env('APP_URL').'/storage/motorbikes',
            'visibility' => 'public',
            'throw' => false,
        ],

        // 's3' => [
        //     'driver' => 's3',
        //     'key' => env('AWS_ACCESS_KEY_ID'),
        //     'secret' => env('AWS_SECRET_ACCESS_KEY'),
        //     'region' => env('AWS_DEFAULT_REGION'),
        //     'bucket' => env('AWS_BUCKET'),
        //     'url' => env('AWS_URL'),
        //     'endpoint' => env('AWS_ENDPOINT'),
        //     'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        //     'throw' => false,
        // ],

        's3' => [
            'driver' => 's3',
            'key' => env('DO_SPACES_KEY'),
            'secret' => env('DO_SPACES_SECRET'),
            'region' => env('DO_SPACES_REGION'),      // required even for DO Spaces
            'bucket' => env('DO_SPACES_BUCKET'),
            'endpoint' => 'https://lon1.digitaloceanspaces.com',  // DO Spaces URL
            'url' => env('DO_SPACES_ENDPOINT'),
            'use_path_style_endpoint' => false,
        ],

    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
        public_path('uploads') => storage_path('app/uploads'),
        public_path('documents') => storage_path('app/documents'),
        public_path('avatars') => storage_path('app/avatars'),
        public_path('signatures') => storage_path('app/signautures'),
    ],

];
