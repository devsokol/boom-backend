<?php

return [

    // there are two options: sms, email
    'verification_available_gateways' => ['email'],

    'verification_via_gateway' => env('VERIFICATION_VIA_GATEWAY', 'email'),

    'verification_code_lifetime' => env('VERIFICATION_CODE_LIFETIME', 600),

    'verification_code_repeat_delay' => env('VERIFICATION_CODE_REPEAT_DELAY', 60),

];
