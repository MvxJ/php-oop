<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

class PDOQueryBuilder extends QueryBuilder
{
    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    public function affected(): int
    {
        return $this->count();
    }

    public function get()
    {
        return $this->statement->fetchAll();
    }

    public function count(): int
    {
        return $this->statement->rowCount();
    }

    public function lastInsertedId()
    {
        return $this->connection->lastInsertId();
    }

    public function prepare($query)
    {
        return $this->connection->prepare($query);
    }

    public function execute($statement)
    {
        $statement->execute($this->bindings);
        $this->bindings = [];
        $this->placeholders = [];

        return $statement;
    }

    public function fetchInfo($className)
    {
        return $this->statement->fetchAll(PDO::FETCH_CLASS, $className);
    }
}
