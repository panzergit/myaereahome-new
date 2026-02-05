<?php

return [
    'server_id' => env('SERVER_ID', 'primary'),

    'ignore_tables' => [
        'change_logs',
        'failed_jobs',
        'migrations',
        'password_resets',
    ],

    'batch_size' => 100,

    // Used during sync:pull
    'disable_logging' => false,
];
