<?php

namespace Portavice\LaravelMailLogger\Tests\Feature;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Monolog\LogRecord;
use Orchestra\Testbench\TestCase;
use Portavice\LaravelMailLogger\Monolog\EmailHandler;
use Psr\Log\LogLevel;
use Symfony\Component\Mailer\Transport\TransportInterface;

class LoggerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $deduplicateStore = __DIR__ . '/../deduplicate.log';

        if (file_exists($deduplicateStore) && is_writable($deduplicateStore)) {
            unlink($deduplicateStore);
        }
    }

    public function testMailerSkippedIfRecipientsAreNotConfigured()
    {
        Mail::shouldReceive('driver')
            ->never();

        $handler = new EmailHandler('default');

        $record = new LogRecord(new CarbonImmutable(), 'default', \Monolog\Level::Error, 'Message');

        $handler->handle($record);
    }

    public function testEmailIsSent()
    {
        Mail::shouldReceive('driver')
            ->withArgs(['smtp'])
            ->once()
            ->andReturnUsing(
                fn () => \Mockery::mock(Mailer::class)
                    ->shouldReceive('getSymfonyTransport')
                    ->once()
                    ->andReturnUsing(
                        fn () => \Mockery::mock(TransportInterface::class)
                            ->shouldReceive('send')
                            ->once()
                            ->getMock()
                    )
                    ->getMock()
            );

        $handler = new EmailHandler(
            'smtp',
            'john.doe@example.org',
            false,
            LogLevel::ERROR
        );

        $record = new LogRecord(CarbonImmutable::now(), 'smtp', \Monolog\Level::Error, 'Message');

        $handler->handle($record);
    }

    public function testEmailIsNotSentIfLogLevelIsTooLow()
    {
        Mail::shouldReceive('driver')
            ->once()
            ->andReturnUsing(
                fn () => \Mockery::mock(Mailer::class)
                    ->shouldReceive('getSymfonyTransport')
                    ->once()
                    ->andReturnUsing(
                        fn () => \Mockery::mock(TransportInterface::class)
                            ->shouldReceive('send')
                            ->never()
                            ->getMock()
                    )
                    ->getMock()
            );

        $handler = new EmailHandler(
            'default',
            'john.doe@example.org',
            false,
            LogLevel::ERROR
        );

        $record = new LogRecord(CarbonImmutable::now(), 'default', \Monolog\Level::Warning, 'Warning Message');

        $handler->handle($record);
    }

    public function testEmailIsSentUntilLogLevelAppears()
    {
        Mail::shouldReceive('driver')
            ->once()
            ->andReturnUsing(
                fn () => \Mockery::mock(Mailer::class)
                    ->shouldReceive('getSymfonyTransport')
                    ->once()
                    ->andReturnUsing(
                        fn () => \Mockery::mock(TransportInterface::class)
                            ->shouldReceive('send')
                            ->once()
                            ->getMock()
                    )
                    ->getMock()
            );

        $handler = new EmailHandler(
            'default',
            'john.doe@example.org',
            false,
            LogLevel::ERROR
        );

        $handler->handle(new LogRecord(CarbonImmutable::now(), 'default', \Monolog\Level::Info, 'Info Message'));
        $handler->handle(new LogRecord(CarbonImmutable::now(), 'default', \Monolog\Level::Warning, 'Warning Message'));
        $handler->handle(new LogRecord(CarbonImmutable::now(), 'default', \Monolog\Level::Error, 'Error Message'));
    }

    public function testEmailsAreDeduplicated()
    {
        Mail::shouldReceive('driver')
            ->once()
            ->andReturnUsing(
                fn () => \Mockery::mock(Mailer::class)
                    ->shouldReceive('getSymfonyTransport')
                    ->once()
                    ->andReturnUsing(
                        fn () => \Mockery::mock(TransportInterface::class)
                            ->shouldReceive('send')
                            ->once()
                            ->getMock()
                    )
                    ->getMock()
            );

        $handler = new EmailHandler(
            'default',
            'john.doe@example.org',
            true,
            LogLevel::ERROR
        );

        $handler->handle(new LogRecord(CarbonImmutable::now(), 'default', \Monolog\Level::Info, 'Info Message'));
        $handler->handle(new LogRecord(CarbonImmutable::now(), 'default', \Monolog\Level::Warning, 'Warning Message'));
        $handler->handle(new LogRecord(CarbonImmutable::now(), 'default', \Monolog\Level::Error, 'Error Message'));
        $handler->handle(new LogRecord(CarbonImmutable::now(), 'default', \Monolog\Level::Info, 'Info Message'));
        $handler->handle(new LogRecord(CarbonImmutable::now(), 'default', \Monolog\Level::Warning, 'Warning Message'));
        $handler->handle(new LogRecord(CarbonImmutable::now(), 'default', \Monolog\Level::Error, 'Error Message'));
    }

    public function testEmailsAreNotDeduplicatedByDefault()
    {
        Mail::shouldReceive('driver')
            ->once()
            ->andReturnUsing(
                fn () => \Mockery::mock(Mailer::class)
                    ->shouldReceive('getSymfonyTransport')
                    ->once()
                    ->andReturnUsing(
                        fn () => \Mockery::mock(TransportInterface::class)
                            ->shouldReceive('send')
                            ->twice()
                            ->getMock()
                    )
                    ->getMock()
            );

        $handler = new EmailHandler(
            'default',
            'john.doe@example.org',
            false,
            LogLevel::ERROR
        );

        $handler->handle(new LogRecord(CarbonImmutable::now(), 'default', \Monolog\Level::Info, 'Info Message'));
        $handler->handle(new LogRecord(CarbonImmutable::now(), 'default', \Monolog\Level::Warning, 'Warning Message'));
        $handler->handle(new LogRecord(CarbonImmutable::now(), 'default', \Monolog\Level::Error, 'Error Message'));
        $handler->handle(new LogRecord(CarbonImmutable::now(), 'default', \Monolog\Level::Info, 'Info Message'));
        $handler->handle(new LogRecord(CarbonImmutable::now(), 'default', \Monolog\Level::Warning, 'Warning Message'));
        $handler->handle(new LogRecord(CarbonImmutable::now(), 'default', \Monolog\Level::Error, 'Error Message'));
    }

    public function testLogTransportIsNotHandled()
    {
        Config::set('mail.mailers.log.transport', 'log');

        Mail::shouldReceive('driver')
            ->never();

        $handler = new EmailHandler(
            'log',
            'john.doe@example.org',
            false,
            LogLevel::ERROR
        );

        $handler->handle(new LogRecord(CarbonImmutable::now(), 'default', \Monolog\Level::Info, 'Info Message'));
        $handler->handle(new LogRecord(CarbonImmutable::now(), 'default', \Monolog\Level::Warning, 'Warning Message'));
        $handler->handle(new LogRecord(CarbonImmutable::now(), 'default', \Monolog\Level::Error, 'Error Message'));
        $handler->handle(new LogRecord(CarbonImmutable::now(), 'default', \Monolog\Level::Info, 'Info Message'));
        $handler->handle(new LogRecord(CarbonImmutable::now(), 'default', \Monolog\Level::Warning, 'Warning Message'));
        $handler->handle(new LogRecord(CarbonImmutable::now(), 'default', \Monolog\Level::Error, 'Error Message'));
    }
}
