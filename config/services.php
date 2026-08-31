<?php

use App\Enums\Plex;

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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'discord' => [
        'webhook_url' => env('DISCORD_WEBHOOK_URL'),
    ],

    'pocketid' => [
        'use_pkce' => env('POCKETID_USE_PKCE', false),
        'base_url' => env('POCKETID_BASE_URL'),
        'client_id' => env('POCKETID_CLIENT_ID'),
        'client_secret' => env('POCKETID_CLIENT_SECRET'),
        'redirect' => env('POCKETID_REDIRECT_URI'),
    ],

    'plex' => [
        'libraries' => ['Movies', 'Music', 'Shows'],
        'enabled_types' => [
            Plex\MetadataType::ALBUM,
            Plex\MetadataType::ARTIST,
            Plex\MetadataType::EPISODE,
            Plex\MetadataType::MOVIE,
            // Plex\MetadataType::SHOW,
            // Plex\MetadataType::TRACK,
        ],
    ],
];
