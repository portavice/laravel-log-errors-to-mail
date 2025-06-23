<?php

return [
    'email_driver' => env('LOG_ERROR_TO_MAIL_MAILER', config('mail.default')),

    'recipient' => env('LOG_ERROR_TO_MAIL_TO'),

    'deduplicate' => env('LOG_ERROR_TO_MAIL_DEDUPLICATE', true),

    'deduplicate_path' => env('LOG_ERROR_TO_MAIL_DEDUPLICATE_PATH', storage_path('logs/deduplicate.log')),

    'deduplicate_time' => env('LOG_ERROR_TO_MAIL_DEDUPLICATE_TIME', 60),

    'deduplicate_bubble' => env('LOG_ERROR_TO_MAIL_DEDUPLICATE_BUBBLE', true),

    'log_level' => env('LOG_ERROR_TO_MAIL_LEVEL', \Psr\Log\LogLevel::ERROR),
];
