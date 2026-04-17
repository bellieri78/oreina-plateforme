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

    'helloasso' => [
        'client_id' => env('HELLOASSO_CLIENT_ID'),
        'client_secret' => env('HELLOASSO_CLIENT_SECRET'),
        'organization_slug' => env('HELLOASSO_ORGANIZATION_SLUG', 'oreina'),
        'webhook_secret' => env('HELLOASSO_WEBHOOK_SECRET'),
        'api_url' => env('HELLOASSO_API_URL', 'https://api.helloasso.com'),
    ],

    'crossref' => [
        'username' => env('CROSSREF_USERNAME'),
        'password' => env('CROSSREF_PASSWORD'),
        'deposit_url' => env('CROSSREF_DEPOSIT_URL', 'https://doi.crossref.org/servlet/deposit'),
        'doi_prefix' => env('CROSSREF_DOI_PREFIX', '10.24349'),
        'registrant' => env('CROSSREF_REGISTRANT', 'OREINA'),
        'dry_run' => env('CROSSREF_DRY_RUN', true),
    ],

    'latex' => [
        // 'local' = use local pdflatex, 'api' = use YtoTech external API
        'driver' => env('LATEX_DRIVER', 'local'),
        'pdflatex_path' => env('LATEX_PDFLATEX_PATH', '/usr/bin/pdflatex'),
        'api_url' => env('LATEX_API_URL', 'https://latex.ytotech.com/builds/sync'),
        'api_timeout' => env('LATEX_API_TIMEOUT', 120),
    ],

    'turnstile' => [
        'site_key'   => env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
        'enabled'    => env('TURNSTILE_ENABLED', true),
    ],

    'anthropic' => [
        'api_key' => env('ANTHROPIC_PLATFORM_KEY'),
    ],

];
