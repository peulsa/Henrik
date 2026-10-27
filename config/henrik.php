<?php

return [
    'auth' => [
        'shared_secret' => env('HENRIK_PLATFORM_SHARED_SECRET'),
        'allow_local_identity' => env('HENRIK_ALLOW_LOCAL_IDENTITY', false),
        'local_email' => env('HENRIK_LOCAL_USER_EMAIL', 'desarrollo@donihue.cl'),
        'local_name' => env('HENRIK_LOCAL_USER_NAME', 'Desarrollo local'),
    ],
];
