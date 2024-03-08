<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Exception\NotFoundException;

class Config
{
    public static function get(string $fileName, ?string $key = null): mixed
    {
        $fileContent = self::getFileConfig($fileName);

        if (!$key) {
            return $fileContent;
        }

        return array_key_exists($key, $fileContent) ? $fileContent[$key] : [];
    }
    public static function getFileConfig(string $fileName): array
    {
        $fileContent = [];

        try {
            $path = realpath(sprintf(__DIR__ . '/../Configs/%s.php', $fileName));
            file_exists($path) ? $fileContent = require $path : [];
        } catch (\Throwable $exception) {
            throw new NotFoundException(
                sprintf('The specified file %s was no found', $fileName),
                ['not found file', 'data is passed']
            );
        }

        return $fileContent;
    }
}