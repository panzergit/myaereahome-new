<?php

return [
    'server_id' => env('SERVER_ID', 'primary'),

    'api_token' => env('SYNC_API_TOKEN', ''),

    'ignore_tables' => [
        'change_logs',
        'failed_jobs',
        'migrations',
        'password_resets',
    ],

    'batch_size' => 100,

    // Used during sync:pull
    'disable_logging' => false,

    'disable_push' => env('DISABLE_PUSH', false),

    'secondary' => [
        'sync_url' => env('SECONDARY_SYNC_URL',''),
    ],
];
