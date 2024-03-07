<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

$application = new \App\Helpers\App();
echo $application->getEnvironment() . PHP_EOL;
echo $application->getServerTime()->format('d-m-Y') . PHP_EOL;
echo $application->getLogPath() . PHP_EOL;
echo $application->isDebugMode() . PHP_EOL;
echo $application->isExecutedFromCommandLine() ? '1' : '0' . PHP_EOL;