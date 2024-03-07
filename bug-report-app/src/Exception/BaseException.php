<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

abstract class BaseException extends Exception
{
    protected array $data = [];

    public function __construct(
        string $message = "",
        array $data = [],
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $this->data = $data;
        parent::__construct($message, $code, $previous);
    }

    public function setData(string $key, mixin $value): void
    {
        $this->data[$key] = $value;
    }

    public function getExtraData(): array
    {
        if (count($this->data) === 0) {
            return $this->data;
        }

        return json_decode(json_encode($this->data), true);
    }
}