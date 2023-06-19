<?php

return [

    'client' => env('SMS_GATEWAY', 'insights'),

    'sms_mailing_chunk_size' => env('SMS_MAILING_CHUNK_SIZE', 50),

    'insights' => [
        'access_token' => env('SMS_INSIGHTS_ACCESS_TOKEN', ''),
        'alpha' => env('SMS_INSIGHTS_ALPHA', 'Testing'),
    ],
];
