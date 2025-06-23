<?php

namespace Portavice\Laravel\LogErrorsToMail\Monolog;

use Illuminate\Support\Facades\Mail;
use Monolog\Handler\DeduplicationHandler;
use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\NoopHandler;
use Monolog\Handler\ProcessableHandlerInterface;
use Monolog\Handler\ProcessableHandlerTrait;
use Monolog\Handler\SymfonyMailerHandler;
use Monolog\Handler\WhatFailureGroupHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;
use Portavice\Laravel\LogErrorsToMail\Monolog\Formatter\HtmlFormatter;
use Psr\Log\LogLevel;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class EmailHandler implements HandlerInterface, ProcessableHandlerInterface
{
    use ProcessableHandlerTrait;

    private HandlerInterface $handler;

    public function __construct(
        ?string $driver,
        ?string $recipient = null,
        bool $deduplicate = false,
        string $level = LogLevel::ERROR
    ) {
        if ($driver === null) {
            $driver = Mail::getDefaultDriver();
        }

        $transport = config('mail.mailers.' . $driver . '.transport');
        if ($transport === 'log') {
            $this->handler = new NoopHandler();
            return;
        }

        // don't try to create an email handler unless mail transport is smtp.
        // otherwise create a noop handler.
        if ($recipient !== null && $driver !== null) {
            $email = new Email();
            $email->from(new Address(config('mail.from.address'), config('mail.from.name', '')));
            $email->to(...explode(',', $recipient));

            $replacements = collect([
                'APP_NAME' => config('app.name'),
                'APP_BASEURL' => config('app.url'),
            ]);

            $email->subject(str_replace(
                $replacements->keys()->map(fn ($k) => '{{' . $k . '}}')->all(),
                $replacements->values()->all(),
                '{{APP_NAME}} [%datetime%] %channel%.%level_name%: %message%'
            ));

            $monologLevel = Logger::toMonologLevel($level);

            $mailHandler = new SymfonyMailerHandler(
                Mail::driver($driver)->getSymfonyTransport(),
                $email,
                $monologLevel,
            );

            $mailHandler->setFormatter(new HtmlFormatter('Y-m-d H:i:s'));

            if ($deduplicate) {
                $deduplicationHandler = new DeduplicationHandler(
                    $mailHandler,
                    // Put the deduplication store into the tests directory
                    (app()->runningUnitTests() ? __DIR__ . '/../../tests/deduplicate.log' : config('laravel_log_errors_to_mail.deduplicate_path')),
                    // try to deduplicate all log levels.
                    Level::Debug,
                    config('laravel_log_errors_to_mail.deduplicate_time', 60),
                    config('laravel_log_errors_to_mail.deduplicate_bubble', true)
                );
            } else {
                $deduplicationHandler = $mailHandler;
            }

            $this->handler = new WhatFailureGroupHandler([
                new FingersCrossedHandler(
                    $deduplicationHandler,
                    new ErrorLevelActivationStrategy($monologLevel), // Buffer all until configured level is reached.
                ),
            ]);
        } else {
            $this->handler = new NoopHandler();
        }
    }

    public function isHandling(LogRecord $record): bool
    {
        return $this->handler->isHandling($record);
    }

    public function handle(LogRecord $record): bool
    {
        if (\count($this->processors) > 0) {
            $record = $this->processRecord($record);
        }

        return $this->handler->handle($record);
    }

    public function handleBatch(array $records): void
    {
        $this->handler->handleBatch($records);
    }

    public function close(): void
    {
        $this->handler->close();
    }
}
