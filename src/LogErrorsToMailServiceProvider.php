<?php

namespace Portavice\Laravel\LogErrorsToMail;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;

class LogErrorsToMailServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/laravel_log_errors_to_mail.php' => config_path('laravel_log_errors_to_mail.php'),
            ], 'laravel-log-errors-to-mail');

            AboutCommand::add('Portavice Log Errors to Mail Package', fn () => ['Version' => '1.0.0']);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel_log_errors_to_mail.php', 'laravel_log_errors_to_mail');
        $this->mergeConfigFrom(__DIR__ . '/../config/channels.php', 'logging.channels');
    }
}
