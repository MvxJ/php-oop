<?php

declare(strict_types=1);

namespace App\Logger;

use App\Contracts\LoggerInterface;
use App\Exception\InvalidLogLevelArgumentException;
use App\Helpers\App;
use ReflectionClass;

class Logger implements LoggerInterface
{
    public function emergency(string $message, array $context = []): void
    {
        $this->addRecord(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert(string $message, array $context = []): void
    {
        $this->addRecord(LogLevel::ALERT, $message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->addRecord(LogLevel::CRITICAL, $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->addRecord(LogLevel::ERROR, $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->addRecord(LogLevel::WARNING, $message, $context);
    }

    public function notice(string $message, array $context = []): void
    {
        $this->addRecord(LogLevel::NOTICE, $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->addRecord(LogLevel::INFO, $message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->addRecord(LogLevel::DEBUG, $message, $context);
    }

    public function log(string $level, string $message, ?array $context = []): void
    {
        $object = new ReflectionClass(LogLevel::class);
        $validLogLevels = $object->getConstants();

        if (!in_array($level, $validLogLevels)) {
            throw new InvalidLogLevelArgumentException($level, $validLogLevels);
        }

        $this->addRecord($level, $message, $context);
    }

    private function addRecord(string $level, string $message, ?array $context = [])
    {
        $application = new App();
        $date = $application->getServerTime()->format('d-m-Y H:i:s');
        $logPath = $application->getLogPath();
        $env = $application->getEnvironment();

        $details = sprintf(
            '%s - Level: %s - Message %s - Context: %s',
            $date,
            $level,
            $message,
            json_encode($context)
        ) . PHP_EOL;

        $fileName = sprintf(
            '%s/%s-%s.log',
            $logPath,
            $env,
            date('dmY')
        );

        file_put_contents($fileName, $details, FILE_APPEND);
    }
}
