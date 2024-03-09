<?php

namespace Tests\Unit;

use App\Contracts\DatabaseConnectionInterface;
use App\Database\MySQLiConnection;
use App\Database\PDOConnection;
use App\Exception\MissingArgumentException;
use App\Helpers\Config;
use PHPUnit\Framework\TestCase;

class DatabaseConnectionTest extends TestCase
{
    public function testItThrowsMissingArgumentExceptionWithWrongCredentialKeys(): void
    {
        self::expectException(MissingArgumentException::class);

        $pdoHandler = new PDOConnection([]);
    }

    public function testItCanConnectToDatabaseWithPdoAPi(): DatabaseConnectionInterface
    {
        $credentials = $this->getCredentials('pdo');
        $pdoHandler = (new PDOConnection($credentials))->connect();

        self::assertInstanceOf(DatabaseConnectionInterface::class, $pdoHandler);

        return $pdoHandler;
    }

    /**
     * @depends testItCanConnectToDatabaseWithPdoAPi
     */
    public function testItIsAValidPdoConnection(DatabaseConnectionInterface $handler): void
    {
        self::assertInstanceOf(\PDO::class, $handler->getConnection());
    }

    public function testItCanConnectToDatabaseWithMySQLiAPi(): DatabaseConnectionInterface
    {
        $credentials = $this->getCredentials('mysqli');
        $handler = (new MySQLiConnection($credentials))->connect();

        self::assertInstanceOf(DatabaseConnectionInterface::class, $handler);

        return $handler;
    }

    /**
     * @depends testItCanConnectToDatabaseWithMySQLiAPi
     */
    public function testItIsAValidMySQLConnection(DatabaseConnectionInterface $handler): void
    {
        self::assertInstanceOf(\mysqli::class, $handler->getConnection());
    }

    private function getCredentials(string $type): array
    {
        return array_merge(
            Config::get('database', $type),
            [
                'db_name' => 'bug_app_test'
            ]
        );
    }
}