<?php

declare(strict_types=1);

namespace App\Helpers;

use DateTimeInterface, DateTime, DateTimeZone;

class App
{
    private array $config = [];

    public function __construct()
    {
        $this->config = Config::getFileConfig('app');
    }

    public function isDebugMode(): bool
    {
        if (!array_key_exists('debug', $this->config)) {
            return false;
        }

        return (bool) $this->config['debug'];
    }

    public function getEnvironment(): string
    {
        if (!array_key_exists('env', $this->config)) {
            return 'production';
        }

        return $this->isTestMode() ? 'test' : $this->config['env'];
    }

    public function getLogPath(): string
    {
        if (!array_key_exists('log_path', $this->config)) {
            throw new \Exception('Log path is not defined');
        }

        return $this->config['log_path'];
    }

    public function isExecutedFromCommandLine(): bool
    {
        return php_sapi_name() === 'cli' || php_sapi_name() === 'phpdbg';
    }

    public function getServerTime(): DateTimeInterface
    {
        return new DateTime('now', new DateTimeZone('Europe/Berlin'));
    }

    public function isTestMode(): bool
    {
        if (!$this->isExecutedFromCommandLine()) {
            return false;
        }

        return (defined('PHPUNIT_RUNNING') && PHPUNIT_RUNNING === true);
    }
}