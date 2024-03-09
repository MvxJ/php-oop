<?php

namespace Tests\Unit;

use App\Contracts\LoggerInterface;
use App\Exception\InvalidLogLevelArgumentException;
use App\Helpers\App;
use App\Logger\Logger;
use App\Logger\LogLevel;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    protected Logger $logger;

    public function setUp(): void
    {
        $this->logger = new Logger();
        parent::setUp();
    }

    public function testItImplementsTheLoggerInterface(): void
    {
        self::assertInstanceOf(LoggerInterface::class, new Logger());
    }

    public function testItCanCreateDifferentTypesOfLogLevels(): void
    {
        $this->logger->info('Testing info logs');
        $this->logger->error('Testing error logs');
        $this->logger->log(LogLevel::ALERT, 'Testing alert logs');
        $application = new App();

        $file = sprintf(
            '%s/%s-%s.log',
            $application->getLogPath(),
            'test',
            date('dmY')
        );

        self::assertFileExists($file);

        $fileContent = file_get_contents($file);

        self::assertStringContainsString('Testing info logs', $fileContent);
        self::assertStringContainsString('Testing error logs', $fileContent);
        self::assertStringContainsString(LogLevel::ALERT, $fileContent);

        unlink($file);

        self::assertFileDoesNotExist($file);
    }

    public function testItThrowsAnInvalidLogLevelArgumentExceptionWhenGivenWrongLogLevel(): void
    {
        self::expectException(InvalidLogLevelArgumentException::class);

        $this->logger->log('invalid_log_level', 'example log message');
    }
}