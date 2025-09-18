<?php

return [
    'base_url' => env('DAJISAAS_BASE_URL', 'https://openapi.dajisaas.com'),
    'app_key' => env('DAJISAAS_APP_KEY'),
    'app_secret' => env('DAJISAAS_APP_SECRET'),
    'timeout' => env('DAJISAAS_TIMEOUT', 20),
    'connect_timeout' => env('DAJISAAS_CONNECT_TIMEOUT', 10),
    'verify_ssl' => env('DAJISAAS_VERIFY_SSL', true),
];


