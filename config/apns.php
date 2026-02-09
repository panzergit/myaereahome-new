<?php

return [
    'team_id' => env('APNS_TEAM_ID'),
    'key_id' => env('APNS_KEY_ID'),
    'bundle_id' => env('APNS_BUNDLE_ID'),
    'key_path' => storage_path('app/apns/AuthKey_TH54DY2JCC.p8'),
    'use_sandbox' => env('APNS_SANDBOX', false),
];

?>