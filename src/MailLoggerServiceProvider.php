<?php

namespace Portavice\LaravelMailLogger;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;

class MailLoggerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/laravel-mail-logger.php' => config_path('laravel-mail-logger.php'),
            ], 'laravel-mail-logger');

            AboutCommand::add('Portavice Laravel E-Mail Logger', fn () => ['Version' => '1.0.0']);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-mail-logger.php', 'laravel-mail-logger');
        $this->mergeConfigFrom(__DIR__ . '/../config/channels.php', 'logging.channels');
    }
}
