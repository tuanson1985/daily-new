<?php
return [
    'napthenhanh' => [
        'url' => env('URL_API_NAPTHENHANH'),
        'url_create_user' => env('URL_API_CREATE_USER_NAPTHENHANH'),
        'partner_id' =>env('PARTNER_ID_NAPTHENHANH'),
        'partner_key' => env('PARTNER_KEY_NAPTHENHANH')
    ],
    'cancaucom' => [
        'url' => env('URL_API_CANCAUCOM'),
        'partner_id' =>env('PARTNER_ID_CANCAUCOM'),
        'url_create_user' => env('URL_API_CREATE_USER_CANCAUCOM'),
        'partner_key' => env('PARTNER_KEY_CANCAUCOM')
    ],
    'paypaypay' => [
        'url' => env('URL_API_PAYPAYPAY'),
        'partner_id' =>env('PARTNER_ID_PAYPAYPAY'),
        'url_create_user' => env('URL_API_CREATE_USER_PAYPAYPAY'),
        'partner_key' => env('PARTNER_KEY_PAYPAYPAY')
    ],
];