<?php

declare(strict_types=1);

set_exception_handler([new \App\Exception\ExceptionHandler(), 'handle']);
set_error_handler([new \App\Exception\ExceptionHandler(), 'convertWarningAndNoticeToException']);