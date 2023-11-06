# E-Mail Logging for Laravel

[![MIT Licensed](https://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

## Installation

```sh
composer require portavice/laravel-mail-logger
```

## Configuration

To configure your Laravel application to use the logger, you should create a logging channel in your `logging.php` configuration file.

For example a stack channel that logs to the default stack and sends email notifications:

```php
return [
    // ...
    'channels' => [
        // ...    

        'stack_with_email' => [
            'driver' => 'stack',
            'channels' => ['stack', 'laravel_mail_logger'],
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
# recipient of the error emails (blank by default) 
LOG_MAIL_TO=
# deduplicate error emails (on by default)
LOG_MAIL_DEDUPLICATE=true
# minimum PSR log level to send emails for (error by default) 
LOG_MAIL_LEVEL=error
```

It's also possible to publish the configuration for this package with the `artisan vendor:publish` command.

```sh
$ php artisan vendor:publish --tag=laravel-mail-logger
```
