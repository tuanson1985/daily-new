<?php
return [

    'active'=>true,
    'site_secret' => env('SITE_SECRET',"a123b456"),

    'encrypt_input'=>[
        'password',
        'password_confirmation',
        'password2',
        'password2_confirmation',
    ]

];
