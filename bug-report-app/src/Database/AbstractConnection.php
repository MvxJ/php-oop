<?php

declare(strict_types=1);

namespace App\Database;

use App\Contracts\DatabaseConnectionInterface;
use App\Exception\MissingArgumentException;

abstract class AbstractConnection
{
    public const REQUAIRED_CONNECTION_KEYS = [];

    protected $connection;
    protected array $credentials = [];

    public function __construct(array $credentials)
    {
        $this->credentials = $credentials;

        if (!$this->credentialsHaveRequiredKeys($this->credentials)) {
            throw new MissingArgumentException(
                sprintf(
                'Database connection credentials are missing, requaired keys: %s',
                    implode(',', static::REQUAIRED_CONNECTION_KEYS)
                )
            );
        }
    }

    private function credentialsHaveRequiredKeys(array $credentials): bool
    {
        $matches = array_intersect_key(array_keys($credentials), static::REQUAIRED_CONNECTION_KEYS);

        return count($matches) === count(static::REQUAIRED_CONNECTION_KEYS);
    }

    abstract protected function parseCredentials(array $credentials): array;
}