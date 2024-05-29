<?php

return [
    'log_errors_to_mail' => [
        'driver' => 'monolog',
        'handler' => \Portavice\Laravel\LogErrorsToMail\Monolog\EmailHandler::class,
        'with' => [
            'driver' => config('laravel-mail-logger.email_driver'),
            'recipient' => config('laravel-mail-logger.recipient'),
            'deduplicate' => config('laravel-mail-logger.deduplicate', true),
            'level' => config('laravel-mail-logger.log_level', \Psr\Log\LogLevel::ERROR),
        ],
    ],
];
