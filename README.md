# Log Errors to E-Mail for Laravel

[![MIT Licensed](https://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

This package adds a basic logging channel that sends E-Mails for messages from a specific error level upwards to 
preconfigured recipients using a Laravel mail transport.

## Prerequisites

* A configured default Laravel mail driver

## Installation

```sh
composer require portavice/laravel-log-errors-to-mail
```

## Configuration

To configure your Laravel application to use the logger, you should create a logging channel in your `logging.php`
configuration file.

For example a stack channel that logs to the default stack and sends email notifications:

```php
return [
    // ...
    'channels' => [
        // ...    

        'stack_with_email' => [
            'driver' => 'stack',
            'channels' => ['stack', 'log_errors_to_mail'],
            'ignore_exceptions' => false,
        ],
    ],
    // ...    
];
```

You may then set the logging channel in your `.env` file or as the default logging channel in your `logging.php`.

```dotenv
LOG_CHANNEL=stack_with_email
```

### Customization

The library offers some customization for the default `laravel_mail_logger` channel via environment variables.

```dotenv
# defaults
# the name of the laravel mailer to use when sending email. (blank by default)
 # if omitted, uses the global default mailer configured for your laravel application 
LOG_ERROR_TO_MAIL_MAILER=
# recipient of the error emails (blank by default) 
LOG_ERROR_TO_MAIL_TO=
# deduplicate error emails (on by default)
LOG_ERROR_TO_MAIL_DEDUPLICATE=true
# minimum PSR log level to send emails for (error by default) 
LOG_ERROR_TO_MAIL_LEVEL=error
```

It's also possible to publish the configuration for this package with the `artisan vendor:publish` command.

```sh
$ php artisan vendor:publish --tag=laravel-log-errors-to-mail
```

### Choosing the Mail Transport

By default, the application uses the default mail driver of your Laravel application.

To change the driver used, you may publish the logger configuration and change the "laravel_log_errors_to_mail.email_driver" 
option to the mail driver name you desire.

The mail driver should extend the `\Illuminate\Mail\Mailer` class and return 
a valid `\Symfony\Component\Mailer\Transport\TransportInterface` instance from the `Mailer::getSymfonyTransport()`
Method.

## Known issues

### Mail drivers using a 'log' transport

Mail drivers using a `\Illuminate\Mail\Transport\LogTransport` transport are not supported and the EmailHandler will
fall back to a `NoopHandler`.

**However**, this automatic fallback currently only works if the selected driver directly uses a `LogTransport`.
If you for example set a `RoundRobinTransport` with a `LogTransport` mail driver, it will end up in 
an infinite recursion loop. 
