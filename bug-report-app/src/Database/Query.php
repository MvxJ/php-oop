<?php

declare(strict_types=1);

namespace App\Database;


trait Query
{
    public function getQuery(string $type)
    {
        $query = match ($type) {
            self::DML_TYPE_SELECT => sprintf(
                'SELECT %s FROM %s WHERE %s',
                $this->fields,
                $this->table,
                implode(' AND ', $this->placeholders)
            ),
            self::DML_TYPE_INSERT => sprintf(
                'INSERT INTO %s (%s) VALUES (%s)',
                $this->table,
                $this->fields,
                implode(',', $this->placeholders)
            ),
            self::DML_TYPE_UPDATE => sprintf(
                'UPDATE %s SET %s WHERE (%s)',
                $this->table,
                implode(', ', $this->fields),
                implode(' AND ', $this->placeholders)
            ),
            self::DML_TYPE_DELETE => sprintf(
                'DELETE FROM %s WHERE (%s)',
                $this->table,
                implode(' AND ', $this->placeholders)
            )
        };

        if (!$query) {
            throw new \InvalidArgumentException(
                'Dml type not supported'
            );
        }

        return $query;
    }
}