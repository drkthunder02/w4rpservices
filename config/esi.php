<?php
    return [
        'client_id' => env('ESI_CLIENT_ID'),
        'secret' => env('ESI_SECRET_KEY'),
        'useragent' => env('ESI_USERAGENT'),
        'callback' => env('ESI_CALLBACK_URI'),
        'primary' => env('ESI_PRIMARY_CHAR', 93738489),
        'alliance' => env('ESI_ALLIANCE', 99004116),
        'corporation' => env('ESI_CORPORATION', 98287666)
    ];
?>