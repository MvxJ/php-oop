<?php

namespace App\Exception;

use App\Helpers\App;
use Throwable;
use ErrorException;

class ExceptionHandler
{
    public function handle(Throwable $exception): void
    {
        $application = new App();

        if ($application->isDebugMode()) {
            var_dump($exception);
        } else {
            echo 'Something went wrong. Please try again later.';
        }

        exit();
    }

    public function convertWarningAndNoticeToException(
        int $severity,
        string $message,
        string $file,
        int $lineNumber
    ): void {
        throw new ErrorException($message, $severity, $severity, $file, $lineNumber);
    }
}