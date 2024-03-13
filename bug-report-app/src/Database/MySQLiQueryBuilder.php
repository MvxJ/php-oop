<?php

namespace App\Database;

use App\Exception\MissingArgumentException;
use ReflectionClass;

class MySQLiQueryBuilder extends QueryBuilder
{
    public const PARAM_TYPE_INT = 'i';
    public const PARAM_TYPE_STRING = 's';
    public const PARAM_TYPE_DOUBLE = 'd';

    private $resultSet;
    private $results;

    public function beginTransaction(): void
    {
        $this->connection->begin_transaction();
    }

    public function affected(): int
    {
        $this->statement->store_result();

        return $this->statement->affected_rows;
    }

    public function get()
    {
        $results = [];

        if (!$this->resultSet) {
            $this->resultSet = $this->statement->get_result();
            if ($this->resultSet) {
                while ($object = $this->resultSet->fetch_object()) {
                    $results[] = $object;
                }
            }
        }

        $this->results = $results;

        return $this->results;
    }

    public function count(): int
    {
        if (!$this->resultSet) {
            $this->get();
        }

        return $this->resultSet ? $this->resultSet->num_rows : 0;
    }

    public function lastInsertedId()
    {
        return $this->connection->insert_id;
    }

    public function prepare($query)
    {
        return $this->connection->prepare($query);
    }

    public function execute($statement)
    {
        if (!$statement) {
            throw new MissingArgumentException(
                'MySQLi statement is null, please check the execution stub'
            );
        }

        if ($this->bindings) {
           $bindings = $this->parseBindings($this->bindings);
           $reflectionObj = new ReflectionClass('mysqli_stmt');
           $method = $reflectionObj->getMethod('bind_param');
           $method->invokeArgs($statement, $bindings);
        }

        $statement->execute();
        $this->bindings = [];
        $this->placeholders = [];

        return $statement;
    }

    public function fetchInfo($className)
    {
        $results = [];
        $this->resultSet = $this->statement->get_result();

        while ($object = $this->resultSet->fetch_object($className)) {
            $results[] = $object;
        }

        return $this->results = $results;
    }

    private function parseBindings(array $params)
    {
        $bindings = [];
        $count = count($params);

        if ($count === 0) {
            return $this->bindings;
        }

        $bindingTypes = $this->parseBindingType();
        $bindings[] = & $bindingTypes;

        for ($index = 0; $index < $count; $index++) {
            $bindings[] = & $params[$index];
        }

        return $bindings;
    }

    private function parseBindingType(): string
    {
        $bindingTypes = [];

        foreach ($this->bindings as $binding) {
            if(is_int($binding)){
                $bindingTypes[] = self::PARAM_TYPE_INT;
            }
            if(is_string($binding)){
                $bindingTypes[] = self::PARAM_TYPE_STRING;
            }

            if(is_float($binding)){
                $bindingTypes[] = self::PARAM_TYPE_DOUBLE;
            }
        }

        return implode('', $bindingTypes);
    }
}
