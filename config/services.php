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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'telegram' => [
        'bot_token'      => env('TELEGRAM_BOT_TOKEN', '8758560868:AAHSkhWa4l4bW9qJJxz1tI8CRubE94X3swE'),
        'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET'),
        'chat_id'        => env('TELEGRAM_CHAT_ID', '-5360314363'),
    ],

    'openrouter' => [
        'api_key'                   => env('OPENROUTER_API_KEY'),
        'model'                     => env('OPENROUTER_MODEL', 'openai/gpt-4o-mini'),
        'base_url'                  => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
        'timeout'                   => (int) env('OPENROUTER_TIMEOUT', 15),
        'retries'                   => (int) env('OPENROUTER_RETRIES', 2),
        'max_tokens'                => (int) env('OPENROUTER_MAX_TOKENS', 1000),
        'history_limit'             => (int) env('OPENROUTER_HISTORY_LIMIT', 10),
        'cost_per_1k_input_tokens'  => (float) env('OPENROUTER_COST_INPUT_1K', 0.00015),
        'cost_per_1k_output_tokens' => (float) env('OPENROUTER_COST_OUTPUT_1K', 0.0006),
    ],

];
