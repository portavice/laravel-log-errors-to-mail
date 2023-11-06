<?php

return [
    'laravel_mail_logger' => [
        'driver' => 'monolog',
        'handler' => \Portavice\LaravelMailLogger\Monolog\EmailHandler::class,
        'with' => [
            'driver' => config('laravel-mail-logger.email_driver'),
            'recipient' => config('laravel-mail-logger.recipient'),
            'deduplicate' => config('laravel-mail-logger.deduplicate', true),
            'level' => config('laravel-mail-logger.log_level', \Psr\Log\LogLevel::ERROR),
        ],
    ],
];
