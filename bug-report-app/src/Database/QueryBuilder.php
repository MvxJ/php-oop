<?php

declare(strict_types=1);

namespace App\Database;

use App\Contracts\DatabaseConnectionInterface;
use App\Exception\InvalidArgumentException;
use App\Exception\NotFoundException;

abstract class QueryBuilder
{
    use Query;

    public const OPERATORS = ['=', '>=', '>', '<=', '<', '<>'];
    public const PLACEHOLDER = '?';
    public const COLUMNS = '*';
    public const DML_TYPE_SELECT = 'SELECT';
    public const DML_TYPE_INSERT = 'INSERT';
    public const DML_TYPE_UPDATE = 'UPDATE';
    public const DML_TYPE_DELETE = 'DELETE';

    protected $connection;
    protected string $table;
    protected $statement;
    protected $fields;
    protected $placeholders;
    protected $bindings;
    protected string $operation = self::DML_TYPE_SELECT;

    abstract public function get();
    abstract public function count(): int;
    abstract public function lastInsertedId();
    abstract public function prepare($query);
    abstract public function execute($statement);
    abstract public function fetchInfo($className);

    public function __construct($databaseConnection)
    {
        $this->connection = $databaseConnection->getConnection();
    }

    public function table(string $tableName): self
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
            throw new InvalidArgumentException(
                'Operators is not valid',
                ['operator' => $operator]
            );
        }

        $this->passWhere([$column => $value], $operator);
        $query = $this->prepare($this->getQuery($this->operation));
        var_dump($query);
        $this->statement = $this->execute($query);

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

    public function create(array $data)
    {
        $this->fields = '`' . implode('`, `', array_keys($data)) . '`';

        foreach ($data as $value) {
            $this->placeholders[] = self::PLACEHOLDER;
            $this->bindings = $value;
        }

        $query = $this->prepare($this->getQuery(self::DML_TYPE_INSERT));
        $this->statement = $this->execute($query);

        return $this->lastInsertedId();
    }

    public function update(array $data): self
    {
        $this->fields = [];
        $this->operation = self::DML_TYPE_UPDATE;

        foreach ($data as $column => $value) {
            $this->fields[] = sprintf('%s%s%s', $column, self::OPERATORS[0], "'$value'");
        }

        return $this;
    }

    public function delete(): self
    {
        $this->operation = self::DML_TYPE_UPDATE;

        return $this;
    }

    public function raw($query)
    {
        $query = $this->prepare($query);
        $this->statement = $this->execute($query);

        return $this;
    }

    public function find($id)
    {
        return $this->where('id', $id)->first();
    }

    public function findOneBy(string $field, $value)
    {
        return $this->where($field, $value)->first();
    }

    public function first()
    {
        return $this->count() ? $this->get()[0] : null;
    }
}