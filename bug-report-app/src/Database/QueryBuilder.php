<?php

declare(strict_types=1);

namespace App\Database;

use App\Contracts\DatabaseConnectionInterface;
use App\Exception\NotFoundException;

class QueryBuilder
{
    use Query;

    public const OPERATORS = ['=', '>=', '>', '<=', '<', '<>'];
    public const PLACEHOLDER = '?';
    public const COLUMNS = '*';
    public const DML_TYPE_SELECT = 'SELECT';
    public const DML_TYPE_INSERT = 'INSERT';
    public const DML_TYPE_UPDATE = 'UPDATE';
    public const DML_TYPE_DELETE = 'DELETE';

    public string $query = '';

    protected $connection;
    protected string $table;
    protected $statement;
    protected $fields;
    protected $placeholders;
    protected $bindings;
    protected string $operation = self::DML_TYPE_SELECT;

    public function __construct(DatabaseConnectionInterface $databaseConnection)
    {
        $this->connection = $databaseConnection->getConnection();
    }

    public function setTable(string $tableName): self
    {
        $this->table = $tableName;

        return $this;
    }

    public function where(
        string $column,
        string $operator = self::OPERATORS[0],
        $value = null
    ): self {
        if (!in_array($operator, self::OPERATORS) && !$value) {
            $value = $operator;
            $operator = self::OPERATORS[0];
        } else {
            throw new NotFoundException(
                'Operators is not valid',
                ['operator' => $operator]
            );
        }

        $this->passWhere([$column => $value], $operator);
        $this->query = $this->prepare($this->getQuery($this->operation));

        return $this;
    }

    public function select(string $fields = self::COLUMNS)
    {
        $this->operation = self::DML_TYPE_SELECT;
        $this->fields = $fields;

        return $this;
    }

    private function passWhere(array $conditions, string $operator): self
    {
        foreach ($conditions as $column => $value) {
            $this->placeholders[] = sprintf(
                '%s %s %s',
                $column,
                $operator,
                self::PLACEHOLDER
            );
            $this->bindings[] = $value;
        }

        return $this;
    }
}