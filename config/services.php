<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google_calendar' => [
        // Backward-compatible: support both GOOGLE_CALENDAR_SYNC_ENABLED and GOOGLE_CALENDAR_ENABLED.
        'enabled' => env('GOOGLE_CALENDAR_SYNC_ENABLED', env('GOOGLE_CALENDAR_ENABLED', false)),
        'calendar_id' => env('GOOGLE_CALENDAR_ID', ''),
        'embed_id' => env('GOOGLE_CALENDAR_EMBED_ID', env('GOOGLE_CALENDAR_ID', '')),
        'holiday_calendar_id' => env('GOOGLE_CALENDAR_HOLIDAY_ID', 'id.indonesian#holiday@group.v.calendar.google.com'),
        'include_holidays_in_embed' => env('GOOGLE_CALENDAR_INCLUDE_HOLIDAYS', true),
        'service_account_json' => env('GOOGLE_CALENDAR_SERVICE_ACCOUNT_JSON', ''),
    ],

];
