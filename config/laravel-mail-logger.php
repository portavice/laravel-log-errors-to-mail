<?php

return [
    'email_driver' => env('LOG_MAIL_MAILER', config('mail.default')),

    'recipient' => env('LOG_MAIL_TO'),

    'deduplicate' => env('LOG_MAIL_DEDUPLICATE', true),

    'log_level' => env('LOG_MAIL_LEVEL', \Psr\Log\LogLevel::ERROR),
];
