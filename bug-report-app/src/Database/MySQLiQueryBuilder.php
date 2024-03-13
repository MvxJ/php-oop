<?php

declare(strict_types = 1);

namespace App\Database;

use App\Exception\MissingArgumentException;

class MySQLiQueryBuilder extends QueryBuilder
{
    public const PARAM_TYPE_INT = 'i';
    public const PARAM_TYPE_STRING = 's';
    public const PARAM_TYPE_DOUBLE = 'd';

    private $resultSet;
    private $results;

    public function get()
    {
        $results = [];

        if (!$this->resultSet) {
            $this->resultSet = $this->statement->get_result();
            if ($this->resultSet) {
                while ($object = $this->resultSet->fetch_object()) {
                    $results[] = $object;
                }
                $this->results = $results;
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
           $reflectionObj = new \ReflectionClass('mysqli_stmt');
           $method = $reflectionObj->getMethod('bind_param');
           $method->invokeArgs($statement, $bindings);
        }

        $statement->execute();
        $this->bindings = [];
        $this->placeholders = [];

        return $statement;
    }

    private function parseBindings(array $params)
    {
        $bindings = [];
        $count = count($params);

        if ($count === 0) {
            return $this->bindings;
        }

        $bindingTypes = $this->parseBindingTypes();
        $bindings[] = & $bindingTypes;

        for ($index = 0; $index < $count; $index++) {
            $bindings[] = & $params[$index];
        }
        return $bindings;
    }

    public function parseBindingTypes(): string
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

    public function fetchInto($className)
    {
        $results = [];
        $this->resultSet = $this->statement->get_result();
        while($object = $this->resultSet->fetch_object($className)){
            $results[] = $object;
        }

        return $this->results = $results;
    }

    public function beginTransaction()
    {
        $this->connection->begin_transaction();
    }

    public function affected()
    {
        $this->statement->store_result();

        return $this->statement->affected_rows;
    }
}